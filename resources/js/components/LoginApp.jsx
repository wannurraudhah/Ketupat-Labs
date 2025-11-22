import React, { useState, useEffect } from 'react';
import axios from 'axios';
import ClassDashboard from './ClassDashboard';
import StudentDashboard from './StudentDashboard';

const apiBase = import.meta.env.VITE_API_BASE_URL ?? '';

export default function LoginApp() {
    const [email, setEmail] = useState('teacher@example.com');
    const [password, setPassword] = useState('secret123');
    const [remember, setRemember] = useState(false);
    const [status, setStatus] = useState('');
    const [loading, setLoading] = useState(false);
    const [user, setUser] = useState(null);
    const [error, setError] = useState('');

    // Helper to extract a helpful message from axios errors
    const extractAxiosMessage = (err) => {
        if (err?.response?.data) {
            if (typeof err.response.data === 'string') return err.response.data;
            return err.response.data.message ?? JSON.stringify(err.response.data);
        }
        return err.message ?? 'Request failed';
    };

    const handleLogin = async (event) => {
        event.preventDefault();
        setLoading(true);
        setStatus('');
        setError('');

        try {
            // Ensure Sanctum CSRF cookie is set
            await axios.get(`${apiBase}/sanctum/csrf-cookie`, { withCredentials: true });

            // Perform login — axios will read the XSRF cookie and set X-XSRF-TOKEN header
            await axios.post(
                `${apiBase}/api/login`,
                { email, password, remember },
                { withCredentials: true },
            );

            // Redirect to dashboard after successful login
            window.location.href = '/dashboard';
        } catch (err) {
            setError(extractAxiosMessage(err));
            setUser(null);
        } finally {
            setLoading(false);
        }
    };

    const handleLogout = async () => {
        setLoading(true);
        setStatus('');
        setError('');

        try {
            await axios.post(`${apiBase}/api/logout`, {}, { withCredentials: true });
            setUser(null);
            setStatus('Logged out.');
        } catch (err) {
            setError(err.message || 'Login error');
        } finally {
            setLoading(false);
        }
    };

    // Check authentication on mount - redirect to dashboard if authenticated on root path
    useEffect(() => {
        let mounted = true;

        (async () => {
            try {
                const res = await axios.get(`${apiBase}/api/user`, { withCredentials: true });
                if (mounted) {
                    setUser(res.data);
                    // If authenticated and on root path, redirect to dashboard
                    if (window.location.pathname === '/') {
                        window.location.href = '/dashboard';
                    }
                }
            } catch (e) {
                // not authenticated — ignore
                if (mounted && window.location.pathname === '/dashboard') {
                    // If on dashboard but not authenticated, redirect to root
                    window.location.href = '/';
                }
            }
        })();

        return () => {
            mounted = false;
        };
    }, []);

    // If not logged in show the login form
    if (!user) {
        return (
            <div className="min-h-screen bg-slate-100 flex items-center justify-center p-6">
                <div className="w-full max-w-md rounded-lg bg-white shadow-md p-6 space-y-6">
                    <header>
                        <h1 className="text-2xl font-semibold text-slate-900">Class Module Login Demo</h1>
                        <p className="text-sm text-slate-500">
                            This lightweight React screen lets you try the Sanctum login flow. Adjust or remove it later.
                        </p>
                    </header>

                    <form onSubmit={handleLogin} className="space-y-4">
                        <div className="space-y-2">
                            <label htmlFor="email" className="block text-sm font-medium text-slate-700">
                                Email
                            </label>
                            <input
                                id="email"
                                type="email"
                                className="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value={email}
                                onChange={(event) => setEmail(event.target.value)}
                                required
                            />
                        </div>
                        <div className="space-y-2">
                            <label htmlFor="password" className="block text-sm font-medium text-slate-700">
                                Password
                            </label>
                            <input
                                id="password"
                                type="password"
                                className="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value={password}
                                onChange={(event) => setPassword(event.target.value)}
                                required
                            />
                        </div>
                        <label className="inline-flex items-center text-sm text-slate-600">
                            <input
                                type="checkbox"
                                className="mr-2 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                checked={remember}
                                onChange={(event) => setRemember(event.target.checked)}
                            />
                            Remember me
                        </label>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                        >
                            {loading ? 'Logging in…' : 'Log In'}
                        </button>
                    </form>

                    <div className="space-y-2">
                        {status && <p className="text-sm text-green-600">{status}</p>}
                        {error && <p className="text-sm text-red-500">{error}</p>}
                    </div>

                    <footer className="text-center text-xs text-slate-400">
                        Update `.env` with `SANCTUM_STATEFUL_DOMAINS` and `APP_URL` if this runs on a different host/port.
                    </footer>
                </div>
            </div>
        );
    }

    // Dashboard view for logged in users
    if (user.role === 'teacher') {
        return <ClassDashboard user={user} onLogout={handleLogout} apiBase={apiBase} />;
    } else {
        return <StudentDashboard user={user} onLogout={handleLogout} apiBase={apiBase} />;
    }
}

