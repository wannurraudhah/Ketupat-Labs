import React, { useState, useEffect } from 'react';

export default function ClassFormModal({ open, initial = null, onClose, onSubmit, setFormErrors }) {
    const [name, setName] = useState('');
    const [subject, setSubject] = useState('');
    const [year, setYear] = useState('');
    const [errors, setErrors] = useState({});

    useEffect(() => {
        if (initial) {
            setName(initial.name || '');
            setSubject(initial.subject || '');
            setYear(initial.year ?? '');
        } else {
            setName('');
            setSubject('');
            setYear('');
        }
        setErrors({});
    }, [initial, open]);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
            <div className="absolute inset-0 bg-black/40" onClick={onClose} />

            <div className="relative z-10 w-full max-w-lg rounded-lg bg-white p-6">
                <h2 className="text-xl font-semibold mb-4">{initial ? 'Edit Class' : 'Add Class'}</h2>

                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        setErrors({});
                        
                        // Frontend validation
                        const newErrors = {};
                        if (!name.trim()) {
                            newErrors.name = 'Class name is required.';
                        }
                        if (!subject.trim()) {
                            newErrors.subject = 'Subject is required.';
                        }
                        if (year && (isNaN(year) || Number(year) < 2000 || Number(year) > 2100)) {
                            newErrors.year = 'Year must be between 2000 and 2100.';
                        }
                        
                        if (Object.keys(newErrors).length > 0) {
                            setErrors(newErrors);
                            return;
                        }
                        
                        onSubmit({ name, subject: subject.trim(), year: year ? Number(year) : null }, setErrors);
                    }}
                    className="space-y-4"
                >
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Name</label>
                        <input
                            value={name}
                            onChange={(e) => {
                                setName(e.target.value);
                                if (errors.name) setErrors({ ...errors, name: null });
                            }}
                            required
                            className={`w-full rounded-md border px-3 py-2 ${
                                errors.name ? 'border-red-500' : 'border-slate-300'
                            }`}
                        />
                        {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Subject</label>
                        <input
                            value={subject}
                            onChange={(e) => {
                                setSubject(e.target.value);
                                if (errors.subject) setErrors({ ...errors, subject: null });
                            }}
                            required
                            className={`w-full rounded-md border px-3 py-2 ${
                                errors.subject ? 'border-red-500' : 'border-slate-300'
                            }`}
                        />
                        {errors.subject && <p className="mt-1 text-sm text-red-600">{errors.subject}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">
                            Year <span className="text-slate-400 font-normal">(optional)</span>
                        </label>
                        <input
                            value={year}
                            onChange={(e) => {
                                const value = e.target.value;
                                // Only allow numbers or empty string
                                if (value === '' || /^\d+$/.test(value)) {
                                    setYear(value);
                                    if (errors.year) setErrors({ ...errors, year: null });
                                }
                            }}
                            type="number"
                            min="2000"
                            max="2100"
                            placeholder="e.g., 2025"
                            className={`w-full rounded-md border px-3 py-2 ${
                                errors.year ? 'border-red-500' : 'border-slate-300'
                            }`}
                        />
                        {errors.year && <p className="mt-1 text-sm text-red-600">{errors.year}</p>}
                    </div>

                    <div className="flex justify-end gap-2">
                        <button type="button" onClick={onClose} className="rounded-md px-3 py-1 border">
                            Cancel
                        </button>
                        <button type="submit" className="rounded-md bg-blue-600 px-3 py-1 text-white">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
