import React, { useState, useEffect } from 'react';
import axios from 'axios';

export default function AddStudentModal({ open, onClose, onSubmit, apiBase, existingStudentIds = [] }) {
    const [students, setStudents] = useState([]);
    const [loading, setLoading] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedStudentId, setSelectedStudentId] = useState('');
    const [error, setError] = useState('');

    useEffect(() => {
        if (open) {
            loadStudents();
        } else {
            setSearchTerm('');
            setSelectedStudentId('');
            setError('');
        }
    }, [open]);

    const loadStudents = async () => {
        setLoading(true);
        setError('');

        try {
            // Get all students (users with role='student')
            // Note: You might want to create a dedicated endpoint for this
            // For now, we'll need to fetch all users and filter, or create an endpoint
            const res = await axios.get(`${apiBase}/api/students`, { withCredentials: true });
            setStudents(res.data || []);
        } catch (err) {
            // If endpoint doesn't exist, we'll handle it differently
            console.error('Error loading students:', err);
            setError('Could not load students. Please ensure students exist in the system.');
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setError('');

        if (!selectedStudentId) {
            setError('Please select a student');
            return;
        }

        if (existingStudentIds.includes(Number(selectedStudentId))) {
            setError('This student is already enrolled in the class');
            return;
        }

        onSubmit(Number(selectedStudentId));
    };

    // Filter students by search term and exclude already enrolled students
    const availableStudents = students.filter(
        (student) =>
            student.role === 'student' &&
            !existingStudentIds.includes(student.id) &&
            (student.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                student.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                student.username?.toLowerCase().includes(searchTerm.toLowerCase()))
    );

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
            <div className="absolute inset-0 bg-black/40" onClick={onClose} />

            <div className="relative z-10 w-full max-w-lg rounded-lg bg-white p-6">
                <h2 className="text-xl font-semibold mb-4">Add Student</h2>

                {error && (
                    <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-md text-red-600 text-sm">
                        {error}
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">
                            Search Students
                        </label>
                        <input
                            type="text"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            placeholder="Search by name, email, or username..."
                            className="w-full rounded-md border border-slate-300 px-3 py-2"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-2">
                            Select Student
                        </label>
                        {loading ? (
                            <div className="text-center py-4 text-slate-500">Loading students...</div>
                        ) : availableStudents.length === 0 ? (
                            <div className="text-center py-4 text-slate-500">
                                {searchTerm
                                    ? 'No students found matching your search.'
                                    : 'No available students to add.'}
                            </div>
                        ) : (
                            <select
                                value={selectedStudentId}
                                onChange={(e) => setSelectedStudentId(e.target.value)}
                                className="w-full rounded-md border border-slate-300 px-3 py-2"
                                required
                            >
                                <option value="">-- Select a student --</option>
                                {availableStudents.map((student) => (
                                    <option key={student.id} value={student.id}>
                                        {student.full_name || student.name} ({student.email})
                                        {student.username ? ` - @${student.username}` : ''}
                                    </option>
                                ))}
                            </select>
                        )}
                    </div>

                    <div className="flex justify-end gap-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-md px-3 py-1 border border-slate-300 text-slate-700 hover:bg-slate-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={!selectedStudentId || loading}
                            className="rounded-md bg-blue-600 px-3 py-1 text-white hover:bg-blue-700 disabled:opacity-60"
                        >
                            Add Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

