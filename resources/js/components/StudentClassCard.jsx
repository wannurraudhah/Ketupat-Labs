import React from 'react';

export default function StudentClassCard({ classroom, apiBase, onView }) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
            <div>
                <h3 className="text-lg font-semibold text-slate-900">{classroom.name}</h3>
                {classroom.subject && <p className="text-sm text-slate-500">{classroom.subject}</p>}
                {classroom.year && <p className="text-xs text-slate-400">Year: {classroom.year}</p>}
            </div>

            <div className="mt-4 flex gap-2 justify-end">
                <button
                    type="button"
                    onClick={() => onView(classroom)}
                    className="rounded-md bg-blue-600 px-3 py-1 text-sm font-medium text-white hover:bg-blue-700"
                >
                    View
                </button>
            </div>
        </div>
    );
}

