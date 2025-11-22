import React, { useState, useEffect } from 'react';
import axios from 'axios';

export default function StudentLessonsList({ classroom, apiBase }) {
    const [lessons, setLessons] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

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

    return (
        <div className="bg-white rounded-lg border border-slate-200 p-6">
            <h2 className="text-xl font-semibold text-slate-900 mb-6">Assigned Lessons</h2>

            {error && (
                <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-600">
                    <strong>Error:</strong> {error}
                </div>
            )}

            {loading ? (
                <div className="text-center py-8 text-slate-500">Loading lessons…</div>
            ) : lessons.length === 0 ? (
                <div className="text-center py-12 text-slate-500">
                    <p>No lessons have been assigned to this class yet.</p>
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
                                        <span className="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                            Published
                                        </span>
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
                                        <div className="mt-3">
                                            <a
                                                href={`${apiBase}/${lesson.material_path}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                                            >
                                                📄 View Material
                                            </a>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

