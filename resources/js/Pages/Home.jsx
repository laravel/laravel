import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function Home({ entries }) {
  const { data, setData, post, processing, reset, errors } = useForm({
    name: '',
    message: '',
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    post('/store', {
      onSuccess: () => reset(),
    });
  };

  return (
    <div className="min-h-screen bg-gray-900 text-white font-sans selection:bg-pink-500 selection:text-white">
      <div className="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 pointer-events-none"></div>

      <div className="relative max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <header className="mb-12 text-center">
            <h1 className="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 via-red-500 to-yellow-500 mb-4 drop-shadow-lg">
                Modern Guestbook
            </h1>
            <p className="text-gray-400 text-lg">Leave a mark in our digital universe.</p>
        </header>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
            {/* Input Section */}
            <div className="relative group">
                <div className="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                <div className="relative px-7 py-8 bg-gray-800/50 backdrop-blur-xl ring-1 ring-gray-900/5 rounded-2xl leading-none flex flex-col space-y-6 shadow-2xl border border-white/10">
                    <h2 className="text-2xl font-bold text-white mb-2">Sign the Guestbook</h2>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-300 mb-1">Name</label>
                            <input
                                type="text"
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all placeholder-gray-500 text-white"
                                placeholder="Your Name"
                            />
                            {errors.name && <div className="text-red-400 text-sm mt-1">{errors.name}</div>}
                        </div>
                        <div>
                            <label htmlFor="message" className="block text-sm font-medium text-gray-300 mb-1">Message</label>
                            <textarea
                                id="message"
                                value={data.message}
                                onChange={(e) => setData('message', e.target.value)}
                                className="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 h-32 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all placeholder-gray-500 text-white resize-none"
                                placeholder="Write something nice..."
                            ></textarea>
                            {errors.message && <div className="text-red-400 text-sm mt-1">{errors.message}</div>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-bold py-3 rounded-lg shadow-lg transform transition hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center"
                        >
                            {processing ? (
                                <svg className="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            ) : (
                                'Sign Guestbook'
                            )}
                        </button>
                    </form>
                </div>
            </div>

            {/* Messages Section */}
            <div className="space-y-6">
                 <h2 className="text-2xl font-bold text-white mb-6">Recent Messages</h2>
                 <div className="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    {entries.length === 0 ? (
                         <div className="text-center text-gray-500 py-10 bg-gray-800/30 rounded-2xl border border-white/5 backdrop-blur-sm">
                            <p>No messages yet. Be the first!</p>
                        </div>
                    ) : (
                        entries.map((entry) => (
                            <div key={entry.id} className="relative group">
                                <div className="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl blur opacity-0 group-hover:opacity-30 transition duration-500"></div>
                                <div className="relative p-5 bg-gray-800/40 backdrop-blur-md border border-white/10 rounded-xl hover:bg-gray-800/60 transition-colors">
                                    <div className="flex justify-between items-start mb-2">
                                        <h3 className="font-bold text-pink-400">{entry.name}</h3>
                                        <span className="text-xs text-gray-500">{new Date(entry.created_at).toLocaleDateString()}</span>
                                    </div>
                                    <p className="text-gray-300 leading-relaxed text-sm">{entry.message}</p>
                                </div>
                            </div>
                        ))
                    )}
                 </div>
            </div>
        </div>
      </div>
      <style>{`
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
      `}</style>
    </div>
  );
}
