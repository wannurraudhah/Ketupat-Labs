import React, { useState, useEffect } from 'react';
import axios from 'axios';
import StudentClassCard from './StudentClassCard';
import StudentClassDetail from './StudentClassDetail';
import AppHeader from './AppHeader';

export default function StudentDashboard({ user, onLogout, apiBase }) {
    const [classes, setClasses] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [viewingClass, setViewingClass] = useState(null);

    const loadClasses = async () => {
        setLoading(true);
        setError('');

        try {
            const res = await axios.get(`${apiBase}/api/classes`, { withCredentials: true });
            setClasses(res.data || []);
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to load classes');
            console.error('Error loading classes:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadClasses();
    }, []);

    // If viewing a class, show the class detail view
    if (viewingClass) {
        return (
            <StudentClassDetail
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
                title="My Classes"
                subtitle="View your enrolled classes and assigned lessons."
                actions={
                    <button onClick={onLogout} className="rounded-md border px-3 py-2">
                        Log out
                    </button>
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
                        <p className="text-slate-500 mb-4">You are not enrolled in any classes yet.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {classes.map((c) => (
                            <StudentClassCard
                                key={c.id}
                                classroom={c}
                                apiBase={apiBase}
                                onView={setViewingClass}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

