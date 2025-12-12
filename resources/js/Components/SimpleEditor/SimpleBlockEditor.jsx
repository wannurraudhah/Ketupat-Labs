import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

// Import the CSS
import '../../../css/block-editor.css';

function SimpleBlockEditor({ initialBlocks = [], inputId = 'content_blocks_input' }) {
    const [blocks, setBlocks] = useState(initialBlocks);

    // Block types available
    const blockTypes = [
        { type: 'text', label: 'Text', icon: '📝' },
        { type: 'heading', label: 'Heading', icon: '📌' },
        { type: 'image', label: 'Image', icon: '🖼️' },
        { type: 'youtube', label: 'YouTube', icon: '▶️' },
    ];

    // Add a new block
    const addBlock = (type) => {
        setBlocks([...blocks, { id: Date.now(), type, content: '' }]);
    };

    // Update block content
    const updateBlock = (id, content) => {
        setBlocks(blocks.map(b => (b.id === id ? { ...b, content } : b)));
    };

    // Remove a block
    const deleteBlock = (id) => {
        setBlocks(blocks.filter(b => b.id !== id));
    };

    // Sync hidden input for form submission
    useEffect(() => {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = JSON.stringify({ blocks });
        }
    }, [blocks, inputId]);

    // Render a single block based on type
    const renderBlock = (block) => {
        switch (block.type) {
            case 'heading':
                return (
                    <input
                        type="text"
                        value={block.content}
                        onChange={(e) => updateBlock(block.id, e.target.value)}
                        placeholder="Heading"
                        style={{ fontSize: '28px', fontWeight: '600' }}
                    />
                );
            case 'image':
                return (
                    <input
                        type="text"
                        value={block.content}
                        onChange={(e) => updateBlock(block.id, e.target.value)}
                        placeholder="Image URL"
                    />
                );
            case 'youtube':
                return (
                    <input
                        type="text"
                        value={block.content}
                        onChange={(e) => updateBlock(block.id, e.target.value)}
                        placeholder="YouTube URL"
                    />
                );
            default:
                return (
                    <textarea
                        value={block.content}
                        onChange={(e) => updateBlock(block.id, e.target.value)}
                        placeholder="Write something..."
                        rows={3}
                    />
                );
        }
    };

    return (
        <div className="block-editor-container">
            {/* Sidebar */}
            <div className="block-editor-sidebar">
                <h3>Content Blocks</h3>
                {blockTypes.map((blockType) => (
                    <button
                        key={blockType.type}
                        type="button"
                        onClick={() => addBlock(blockType.type)}
                        className="block-type-btn"
                    >
                        <span className="icon">{blockType.icon}</span>
                        {blockType.label}
                    </button>
                ))}
            </div>

            {/* Editor Canvas */}
            <div className="block-editor-canvas">
                <div className="block-list">
                    {blocks.length === 0 ? (
                        <div className="editor-empty-state">
                            <h2>Start writing</h2>
                            <p>Click a block type from the sidebar to get started</p>
                        </div>
                    ) : (
                        blocks.map((block) => (
                            <div key={block.id} className="block-item">
                                {renderBlock(block)}
                                <div className="block-controls">
                                    <button
                                        type="button"
                                        onClick={() => deleteBlock(block.id)}
                                        className="block-delete-btn"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}

// Auto-mount when the DOM is ready
function initialize() {
    const container = document.getElementById('block-editor-root');
    if (!container) {
        console.warn('SimpleBlockEditor: #block-editor-root not found');
        return;
    }
    const initial = container.dataset.initialBlocks ? JSON.parse(container.dataset.initialBlocks) : [];
    const root = createRoot(container);
    root.render(<SimpleBlockEditor initialBlocks={initial} />);
    console.log('SimpleBlockEditor mounted');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
} else {
    initialize();
}

export default SimpleBlockEditor;
