import React, { useState, useEffect } from 'react';
import axios from 'axios';
import LessonFormModal from './LessonFormModal';

export default function LessonsList({ classroom, apiBase }) {
    const [lessons, setLessons] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);

    const loadLessons = async () => {
        setLoading(true);
        setError('');

        try {
            const res = await axios.get(`${apiBase}/api/classes/${classroom.id}/lessons`, { withCredentials: true });
            setLessons(res.data || []);
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to load lessons');
            console.error('Error loading lessons:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (classroom?.id) {
            loadLessons();
        }
    }, [classroom?.id]);

    const handleAdd = () => {
        setEditing(null);
        setModalOpen(true);
    };

    const handleEdit = (lesson) => {
        setEditing(lesson);
        setModalOpen(true);
    };

    const handleDelete = async (lesson) => {
        if (!confirm(`Delete lesson "${lesson.title}"?`)) return;

        try {
            await axios.delete(`${apiBase}/api/classes/${classroom.id}/lessons/${lesson.id}`, { withCredentials: true });
            setLessons((s) => s.filter((l) => l.id !== lesson.id));
            setError('');
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to delete lesson');
        }
    };

    const handleSubmit = async (payload, setFormErrors) => {
        try {
            if (editing) {
                const res = await axios.put(
                    `${apiBase}/api/classes/${classroom.id}/lessons/${editing.id}`,
                    payload,
                    { withCredentials: true }
                );
                setLessons((s) => s.map((l) => (l.id === res.data.id ? res.data : l)));
            } else {
                const res = await axios.post(`${apiBase}/api/classes/${classroom.id}/lessons`, payload, {
                    withCredentials: true,
                });
                setLessons((s) => [res.data, ...s]);
            }

            setModalOpen(false);
            setEditing(null);
            setError('');
        } catch (err) {
            if (err?.response?.status === 422 && err?.response?.data?.errors) {
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

    return (
        <div className="bg-white rounded-lg border border-slate-200 p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-xl font-semibold text-slate-900">Assigned Lessons</h2>
                <button
                    onClick={handleAdd}
                    className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Add Lesson
                </button>
            </div>

            {error && (
                <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-600">
                    <strong>Error:</strong> {error}
                </div>
            )}

            {loading ? (
                <div className="text-center py-8 text-slate-500">Loading lessons…</div>
            ) : lessons.length === 0 ? (
                <div className="text-center py-12 text-slate-500">
                    <p className="mb-4">No lessons assigned to this class yet.</p>
                    <button
                        onClick={handleAdd}
                        className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Add Your First Lesson
                    </button>
                </div>
            ) : (
                <div className="space-y-4">
                    {lessons.map((lesson) => (
                        <div
                            key={lesson.id}
                            className="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors"
                        >
                            <div className="flex items-start justify-between">
                                <div className="flex-1">
                                    <div className="flex items-center gap-3 mb-2">
                                        <h3 className="text-lg font-semibold text-slate-900">{lesson.title}</h3>
                                        {lesson.is_published ? (
                                            <span className="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                                Published
                                            </span>
                                        ) : (
                                            <span className="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                                                Draft
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-sm text-slate-600 mb-2">
                                        <span className="font-medium">Topic:</span> {lesson.topic}
                                    </p>
                                    {lesson.duration && (
                                        <p className="text-sm text-slate-500">
                                            <span className="font-medium">Duration:</span> {lesson.duration} minutes
                                        </p>
                                    )}
                                    {lesson.material_path && (
                                        <p className="text-sm text-slate-500 mt-1">
                                            <span className="font-medium">Material:</span> {lesson.material_path}
                                        </p>
                                    )}
                                </div>
                                <div className="flex gap-2 ml-4">
                                    <button
                                        onClick={() => handleEdit(lesson)}
                                        className="rounded-md border border-slate-300 px-3 py-1 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        onClick={() => handleDelete(lesson)}
                                        className="rounded-md bg-red-600 px-3 py-1 text-sm font-medium text-white hover:bg-red-700"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            <LessonFormModal
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

