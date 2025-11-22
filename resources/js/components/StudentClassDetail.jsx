import React, { useState, useEffect } from 'react';
import axios from 'axios';
import StudentLessonsList from './StudentLessonsList';
import AppHeader from './AppHeader';

export default function StudentClassDetail({ classroom, apiBase, onBack }) {
    const [activeView, setActiveView] = useState('lessons');
    const [classData, setClassData] = useState(classroom);

    useEffect(() => {
        if (classroom) {
            setClassData(classroom);
        }
    }, [classroom]);

    return (
        <div className="min-h-screen bg-slate-50">
            <AppHeader
                title={classData?.name}
                subtitle={classData?.subject ? `${classData.subject} (${classData.year})` : null}
                showBack={true}
                onBack={onBack}
            />

            <div className="max-w-7xl mx-auto px-6 py-6 flex gap-6">
                {/* Side Menu */}
                <div className="w-64 flex-shrink-0">
                    <div className="bg-white rounded-lg border border-slate-200 p-4">
                        <nav className="space-y-2">
                            <button
                                onClick={() => setActiveView('lessons')}
                                className={`w-full text-left px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                                    activeView === 'lessons'
                                        ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                        : 'text-slate-700 hover:bg-slate-50'
                                }`}
                            >
                                Lessons
                            </button>
                        </nav>
                    </div>
                </div>

                {/* Main Content */}
                <div className="flex-1">
                    {activeView === 'lessons' && <StudentLessonsList classroom={classData} apiBase={apiBase} />}
                </div>
            </div>
        </div>
    );
}

