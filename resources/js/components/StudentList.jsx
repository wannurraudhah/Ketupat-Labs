import React, { useState, useEffect } from 'react';
import axios from 'axios';
import AddStudentModal from './AddStudentModal';

export default function StudentList({ classroom, apiBase }) {
    const [students, setStudents] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [modalOpen, setModalOpen] = useState(false);

    const loadStudents = async () => {
        setLoading(true);
        setError('');

        try {
            const res = await axios.get(`${apiBase}/api/classes/${classroom.id}/students`, { withCredentials: true });
            setStudents(res.data || []);
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to load students');
            console.error('Error loading students:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (classroom?.id) {
            loadStudents();
        }
    }, [classroom?.id]);

    const handleAddStudent = async (studentId) => {
        try {
            const res = await axios.post(
                `${apiBase}/api/classes/${classroom.id}/students`,
                { student_id: studentId },
                { withCredentials: true }
            );
            setStudents((s) => [...s, res.data]);
            setModalOpen(false);
            setError('');
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to add student');
        }
    };

    const handleRemoveStudent = async (studentId) => {
        if (!confirm('Are you sure you want to remove this student from the class?')) {
            return;
        }

        try {
            await axios.delete(`${apiBase}/api/classes/${classroom.id}/students/${studentId}`, { withCredentials: true });
            setStudents((s) => s.filter((student) => student.id !== studentId));
            setError('');
        } catch (err) {
            setError(err?.response?.data?.message ?? err.message ?? 'Failed to remove student');
        }
    };

    return (
        <div className="bg-white rounded-lg border border-slate-200 p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-xl font-semibold text-slate-900">Student List</h2>
                <button
                    onClick={() => setModalOpen(true)}
                    className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Add Student
                </button>
            </div>

            {error && (
                <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-600">
                    <strong>Error:</strong> {error}
                </div>
            )}

            {loading ? (
                <div className="text-center py-8 text-slate-500">Loading students…</div>
            ) : students.length === 0 ? (
                <div className="text-center py-12 text-slate-500">
                    <p className="mb-4">No students enrolled in this class yet.</p>
                    <button
                        onClick={() => setModalOpen(true)}
                        className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Add Your First Student
                    </button>
                </div>
            ) : (
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Username
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Enrolled At
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-slate-200">
                            {students.map((student) => (
                                <tr key={student.id} className="hover:bg-slate-50">
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm font-medium text-slate-900">
                                            {student.full_name || student.name}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm text-slate-500">{student.email}</div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm text-slate-500">{student.username || '—'}</div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm text-slate-500">
                                            {student.pivot?.enrolled_at
                                                ? new Date(student.pivot.enrolled_at).toLocaleDateString()
                                                : '—'}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button
                                            onClick={() => handleRemoveStudent(student.id)}
                                            className="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <AddStudentModal
                open={modalOpen}
                onClose={() => setModalOpen(false)}
                onSubmit={handleAddStudent}
                apiBase={apiBase}
                existingStudentIds={students.map((s) => s.id)}
            />
        </div>
    );
}

