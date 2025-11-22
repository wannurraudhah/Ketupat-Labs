import React, { useEffect, useState } from 'react';
import axios from 'axios';
import ClassCard from './ClassCard';
import ClassFormModal from './ClassFormModal';
import ClassDetail from './ClassDetail';
import AppHeader from './AppHeader';

export default function ClassDashboard({ user, onLogout, apiBase }) {
    const [classes, setClasses] = useState([]);
    const [loading, setLoading] = useState(false);
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [viewingClass, setViewingClass] = useState(null);
    const [error, setError] = useState('');

    const loadClasses = async () => {
        setLoading(true);
        setError('');

        try {
            console.log('Fetching classes from:', `${apiBase}/api/classes`);
            const res = await axios.get(`${apiBase}/api/classes`, { withCredentials: true });
            console.log('Classes response:', res.data);
            setClasses(res.data || []);
        } catch (err) {
            const errorMessage = err?.response?.data?.message ?? err.message ?? 'Failed to load classes';
            setError(errorMessage);
            console.error('Error loading classes:', err);
            setClasses([]);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        console.log('ClassDashboard mounted, loading classes...', { apiBase, user });
        loadClasses();
    }, [apiBase]);

    const handleAdd = () => {
        setEditing(null);
        setModalOpen(true);
    };

    const handleEdit = (cls) => {
        setEditing(cls);
        setModalOpen(true);
    };

    const handleView = async (cls) => {
        try {
            // Fetch full class details
            const res = await axios.get(`${apiBase}/api/classes/${cls.id}`, { withCredentials: true });
            setViewingClass(res.data);
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to load class details');
        }
    };

    const handleDelete = async (cls) => {
        if (!confirm(`Delete class "${cls.name}"?`)) return;

        try {
            await axios.delete(`${apiBase}/api/classes/${cls.id}`, { withCredentials: true });
            setClasses((s) => s.filter((c) => c.id !== cls.id));
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Delete failed');
        }
    };

    const handleSubmit = async (payload, setFormErrors) => {
        try {
            if (editing) {
                const res = await axios.put(`${apiBase}/api/classes/${editing.id}`, payload, { withCredentials: true });
                setClasses((s) => s.map((c) => (c.id === res.data.id ? res.data : c)));
            } else {
                const res = await axios.post(`${apiBase}/api/classes`, payload, { withCredentials: true });
                setClasses((s) => [res.data, ...s]);
            }

            setModalOpen(false);
            setEditing(null);
            setError('');
        } catch (err) {
            if (err?.response?.status === 422 && err?.response?.data?.errors) {
                // Validation errors from backend
                if (setFormErrors) {
                    const backendErrors = {};
                    Object.keys(err.response.data.errors).forEach((key) => {
                        backendErrors[key] = err.response.data.errors[key][0];
                    });
                    setFormErrors(backendErrors);
                }
            } else {
                setError(err?.response?.data?.message ?? err.message ?? 'Save failed');
            }
        }
    };

    // If viewing a class, show the class detail view
    if (viewingClass) {
        return (
            <ClassDetail
                classroom={viewingClass}
                apiBase={apiBase}
                onBack={() => {
                    setViewingClass(null);
                    loadClasses(); // Refresh classes list
                }}
            />
        );
    }

    return (
        <div className="min-h-screen bg-slate-50">
            <AppHeader
                title="Your Classes"
                subtitle="Manage your classes — add, edit, and remove."
                actions={
                    <>
                        <button onClick={handleAdd} className="rounded-md bg-blue-600 px-4 py-2 text-white">
                            Add Class
                        </button>
                        <button onClick={onLogout} className="rounded-md border px-3 py-2">
                            Log out
                        </button>
                    </>
                }
            />
            <div className="max-w-6xl mx-auto p-6">

                {error && (
                    <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-600">
                        <strong>Error:</strong> {error}
                    </div>
                )}

                {loading ? (
                    <div className="text-center py-8 text-slate-500">Loading classes…</div>
                ) : classes.length === 0 ? (
                    <div className="text-center py-12 bg-white rounded-lg border border-slate-200">
                        <p className="text-slate-500 mb-4">No classes found.</p>
                        <button
                            onClick={handleAdd}
                            className="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                        >
                            Create Your First Class
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {classes.map((c) => (
                            <ClassCard
                                key={c.id}
                                classroom={c}
                                onEdit={handleEdit}
                                onDelete={handleDelete}
                                onView={handleView}
                            />
                        ))}
                    </div>
                )}
            </div>

            <ClassFormModal 
                open={modalOpen} 
                initial={editing} 
                onClose={() => {
                    setModalOpen(false);
                    setEditing(null);
                    setError('');
                }} 
                onSubmit={handleSubmit} 
            />
        </div>
    );
}
