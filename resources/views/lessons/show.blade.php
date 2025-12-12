<x-app-layout>
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex justify-between items-start pb-4 border-b border-gray-200 mb-4">
                <h2 class="text-3xl font-extrabold text-[#2454FF]">
                    {{ $lesson->title }} <span class="text-base text-gray-500">({{ $lesson->topic }})</span>
                </h2>
                <a href="{{ route('lessons.index') }}" class="text-[#5FAD56] hover:text-green-700 font-medium">
                    &larr; Back to Lesson List
                </a>
            </div>

            {{-- PROGRESS TRACKING MOCK (UC004 - Track Progress) --}}
            <div class="progress-bar-area bg-gray-200 h-6 rounded-full mb-6">
                <div class="progress-fill h-full text-center text-white bg-[#5FAD56] rounded-full" style="width: 50%;">
                    50% Complete (Mock Data)
                </div>
            </div>

            <div class="lesson-content-card space-y-6">
                
                <div class="clearfix">
                    <button class="bg-[#F26430] hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg chat-button float-right" onclick="alert('Launching Chatbot Interface (M4)...')">
                        Ask for Help (M4)
                    </button>
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Lesson Content</h3>
                    
                    {{-- Dynamic Block Rendering --}}
                    <div class="mt-6 space-y-6">
                        @if(isset($lesson->content_blocks['blocks']) && count($lesson->content_blocks['blocks']) > 0)
                            @foreach($lesson->content_blocks['blocks'] as $index => $block)
                                <div class="lesson-block">
                                    @if($block['type'] === 'heading')
                                        <h2 class="text-2xl font-bold text-gray-900 mb-3">
                                            {{ $block['content'] }}
                                        </h2>
                                    
                                    @elseif($block['type'] === 'text')
                                        <div class="text-gray-700 prose max-w-none leading-relaxed">
                                            {!! nl2br(e($block['content'])) !!}
                                        </div>
                                    
                                    @elseif($block['type'] === 'youtube')
                                        @php
                                            // Extract YouTube video ID from URL
                                            $videoUrl = $block['content'];
                                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                                            $videoId = $matches[1] ?? null;
                                        @endphp
                                        
                                        @if($videoId)
                                            <div class="video-container my-6">
                                                <h4 class="text-lg font-semibold text-gray-800 mb-3">📹 Video Demonstration</h4>
                                                <div class="relative" style="padding-bottom: 56.25%; height: 0;">
                                                    <iframe 
                                                        src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                        frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen
                                                        class="absolute top-0 left-0 w-full h-full rounded-lg border-4 border-[#F26430]">
                                                    </iframe>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center my-6">
                                                <a href="{{ $block['content'] }}" target="_blank" class="inline-block">
                                                    <img src="https://placehold.co/500x300/F26430/ffffff?text=Click+to+Watch+Video" 
                                                         alt="Video Placeholder" 
                                                         class="border-4 border-[#F26430] cursor-pointer rounded-lg hover:opacity-90 transition-opacity">
                                                </a>
                                                <p class="text-sm text-gray-600 mt-2">Click image to view on YouTube</p>
                                            </div>
                                        @endif
                                    
                                    @elseif($block['type'] === 'image')
                                        <div class="image-container my-6">
                                            <h4 class="text-lg font-semibold text-gray-800 mb-3">🖼️ Visual Guide</h4>
                                            <div class="border-2 border-[#F26430] p-4 rounded-lg bg-red-50">
                                                <img src="{{ $block['content'] }}" 
                                                     alt="Lesson Image" 
                                                     class="w-full h-auto border border-gray-400 rounded mb-3">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback to old content field if no blocks --}}
                            <div class="text-gray-700 prose max-w-none">
                                {!! nl2br(e($lesson->content)) !!}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pt-4">
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Lesson Materials</h3>
                    @if ($lesson->material_path)
                        <p class="mt-2 text-lg">Downloadable Material: 
                            <a href="{{ Storage::url($lesson->material_path) }}" target="_blank" class="text-[#5FAD56] hover:underline font-bold">
                                {{ basename($lesson->material_path) }}
                            </a>
                        </p>
                    @else
                        <p class="mt-2 text-gray-500">No physical material file available for this lesson.</p>
                    @endif
                </div>

                <div class="pt-4">
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Assessment Links</h3>
                    <a href="{{ route('lessons.show', ['lesson' => $lesson->id, 'action' => 'quiz']) }}" class="assessment-button text-white font-bold py-3 px-6 rounded-lg transition ease-in-out duration-150" style="background-color: #2454FF; text-decoration: none;">
                        Start Gamified Quiz (UC007)
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
</x-app-layout>