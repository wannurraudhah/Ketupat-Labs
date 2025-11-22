import React, { useState, useEffect } from 'react';

export default function LessonFormModal({ open, initial = null, onClose, onSubmit }) {
    const [title, setTitle] = useState('');
    const [topic, setTopic] = useState('');
    const [duration, setDuration] = useState('');
    const [materialPath, setMaterialPath] = useState('');
    const [isPublished, setIsPublished] = useState(false);
    const [errors, setErrors] = useState({});

    useEffect(() => {
        if (initial) {
            setTitle(initial.title || '');
            setTopic(initial.topic || '');
            setDuration(initial.duration ?? '');
            setMaterialPath(initial.material_path || '');
            setIsPublished(initial.is_published ?? false);
        } else {
            setTitle('');
            setTopic('');
            setDuration('');
            setMaterialPath('');
            setIsPublished(false);
        }
        setErrors({});
    }, [initial, open]);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
            <div className="absolute inset-0 bg-black/40" onClick={onClose} />

            <div className="relative z-10 w-full max-w-lg rounded-lg bg-white p-6">
                <h2 className="text-xl font-semibold mb-4">{initial ? 'Edit Lesson' : 'Add Lesson'}</h2>

                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        setErrors({});

                        const newErrors = {};
                        if (!title.trim()) {
                            newErrors.title = 'Lesson title is required.';
                        }
                        if (!topic.trim()) {
                            newErrors.topic = 'Lesson topic is required.';
                        }
                        if (duration && (isNaN(duration) || Number(duration) < 1)) {
                            newErrors.duration = 'Duration must be a positive number.';
                        }

                        if (Object.keys(newErrors).length > 0) {
                            setErrors(newErrors);
                            return;
                        }

                        onSubmit(
                            {
                                title: title.trim(),
                                topic: topic.trim(),
                                duration: duration ? Number(duration) : null,
                                material_path: materialPath.trim() || null,
                                is_published: isPublished,
                            },
                            setErrors
                        );
                    }}
                    className="space-y-4"
                >
                    <div>
                        <label className="block text-sm font-medium text-slate-700">Title</label>
                        <input
                            value={title}
                            onChange={(e) => {
                                setTitle(e.target.value);
                                if (errors.title) setErrors({ ...errors, title: null });
                            }}
                            required
                            className={`w-full rounded-md border px-3 py-2 ${
                                errors.title ? 'border-red-500' : 'border-slate-300'
                            }`}
                        />
                        {errors.title && <p className="mt-1 text-sm text-red-600">{errors.title}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">Topic</label>
                        <input
                            value={topic}
                            onChange={(e) => {
                                setTopic(e.target.value);
                                if (errors.topic) setErrors({ ...errors, topic: null });
                            }}
                            required
                            className={`w-full rounded-md border px-3 py-2 ${
                                errors.topic ? 'border-red-500' : 'border-slate-300'
                            }`}
                        />
                        {errors.topic && <p className="mt-1 text-sm text-red-600">{errors.topic}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">
                            Duration (minutes) <span className="text-slate-400 font-normal">(optional)</span>
                        </label>
                        <input
                            value={duration}
                            onChange={(e) => {
                                const value = e.target.value;
                                if (value === '' || /^\d+$/.test(value)) {
                                    setDuration(value);
                                    if (errors.duration) setErrors({ ...errors, duration: null });
                                }
                            }}
                            type="number"
                            min="1"
                            placeholder="e.g., 60"
                            className={`w-full rounded-md border px-3 py-2 ${
                                errors.duration ? 'border-red-500' : 'border-slate-300'
                            }`}
                        />
                        {errors.duration && <p className="mt-1 text-sm text-red-600">{errors.duration}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700">
                            Material Path <span className="text-slate-400 font-normal">(optional)</span>
                        </label>
                        <input
                            value={materialPath}
                            onChange={(e) => setMaterialPath(e.target.value)}
                            placeholder="e.g., storage/lessons/file.pdf"
                            className="w-full rounded-md border border-slate-300 px-3 py-2"
                        />
                    </div>

                    <div>
                        <label className="inline-flex items-center">
                            <input
                                type="checkbox"
                                checked={isPublished}
                                onChange={(e) => setIsPublished(e.target.checked)}
                                className="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2"
                            />
                            <span className="text-sm text-slate-700">Publish lesson (visible to students)</span>
                        </label>
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
                            className="rounded-md bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

