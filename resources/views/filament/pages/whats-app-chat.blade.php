<x-filament-panels::page>
    <style>
        .wa-layout { display: flex; gap: 1.5rem; height: 80vh; }
        .wa-sidebar { width: 340px; min-width: 300px; display: flex; flex-direction: column; border-radius: 0.75rem; background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); overflow: hidden; }
        .wa-chat { flex: 1; display: flex; flex-direction: column; min-width: 0; border-radius: 0.75rem; background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); overflow: hidden; }

        @media (max-width: 768px) {
            .wa-layout { flex-direction: column; height: auto; gap: 0; }
            .wa-sidebar { width: 100%; min-width: 0; max-height: none; border-radius: 0.75rem; }
            .wa-chat { min-height: 70vh; border-radius: 0.75rem; margin-top: 0.75rem; }
            .wa-sidebar.wa-hidden-mobile { display: none; }
            .wa-chat.wa-hidden-mobile { display: none; }
        }
    </style>

    <div class="wa-layout">

        {{-- Contacts Panel --}}
        <div class="wa-sidebar {{ $selectedPhone ? 'wa-hidden-mobile' : '' }}">
            {{-- Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding: 0.75rem 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111;">Chats</h3>
                    <x-filament::badge color="primary">
                        {{ $this->contacts->total() }}
                    </x-filament::badge>
                </div>
                <button wire:click="$refresh" style="padding: 0.375rem; border-radius: 0.5rem; color: #9ca3af; cursor: pointer; border: none; background: none;" title="Refresh">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;" wire:loading.class="animate-spin" wire:target="$refresh">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182M2.985 19.644l3.181-3.182" />
                    </svg>
                </button>
            </div>

            {{-- Search --}}
            <div style="padding: 0.5rem 0.75rem; border-bottom: 1px solid #e5e7eb;">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by name or number..."
                    />
                </x-filament::input.wrapper>
            </div>

            {{-- Contact List --}}
            <div style="flex: 1; overflow-y: auto;">
                @forelse($this->contacts as $contact)
                    @php
                        $lastMsg = $this->getLastMessageForContact($contact->phone_number);
                        $unread = $this->getUnreadCount($contact->phone_number);
                        $isSelected = $selectedPhone === $contact->phone_number;
                    @endphp
                    <div
                        wire:click="selectContact('{{ $contact->phone_number }}')"
                        wire:key="contact-{{ $contact->phone_number }}"
                        style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; cursor: pointer; border-bottom: 1px solid #f3f4f6; {{ $isSelected ? 'background: #fffbeb;' : '' }}"
                        onmouseover="if(!{{ $isSelected ? 'true' : 'false' }}) this.style.background='#f9fafb'"
                        onmouseout="if(!{{ $isSelected ? 'true' : 'false' }}) this.style.background=''"
                    >
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #22c55e; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700; flex-shrink: 0;">
                            {{ strtoupper(substr($contact->contact_name ?? $contact->phone_number, 0, 1)) }}
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <p style="font-size: 0.875rem; font-weight: 500; color: #111; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $contact->contact_name ?? $contact->phone_number }}
                            </p>
                            <p style="font-size: 0.75rem; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ \Illuminate\Support\Str::limit($lastMsg, 35) ?? 'No messages' }}
                            </p>
                        </div>
                        <div style="flex-shrink: 0; text-align: right;">
                            <p style="font-size: 10px; color: #9ca3af;">
                                {{ \Carbon\Carbon::parse($contact->last_message_at)->diffForHumans(null, true, true) }}
                            </p>
                            @if($unread > 0)
                                <x-filament::badge color="success" size="sm">
                                    {{ $unread }}
                                </x-filament::badge>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding: 3rem 0; text-align: center; color: #6b7280; font-size: 0.875rem;">
                        <x-heroicon-o-chat-bubble-left-right style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.75rem; opacity: 0.3;" />
                        <p style="font-weight: 500;">No conversations yet</p>
                        <p style="font-size: 0.75rem; margin-top: 0.25rem;">Incoming messages will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Chat Panel --}}
        <div class="wa-chat {{ !$selectedPhone ? 'wa-hidden-mobile' : '' }}">
            @if($selectedPhone)
                {{-- Chat Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding: 0.75rem 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        {{-- Back button (mobile only) --}}
                        <button
                            wire:click="$set('selectedPhone', null)"
                            style="padding: 0.375rem; border-radius: 0.5rem; color: #6b7280; cursor: pointer; border: none; background: none; display: none;"
                            class="wa-back-btn"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <div style="width: 2.25rem; height: 2.25rem; border-radius: 9999px; background: #22c55e; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; flex-shrink: 0; overflow: hidden;">
                            @if($this->profilePictureUrl)
                                <img src="{{ $this->profilePictureUrl }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" />
                            @else
                                {{ strtoupper(substr($this->selectedContactName, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #111;">{{ $this->selectedContactName }}</p>
                            <p style="font-size: 0.75rem; color: #6b7280;">{{ $selectedPhone }}</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        @if($this->customerOrdersUrl)
                            <x-filament::button
                                :href="$this->customerOrdersUrl"
                                tag="a"
                                target="_blank"
                                size="sm"
                                color="gray"
                                icon="heroicon-m-shopping-bag"
                            >
                                Orders
                            </x-filament::button>
                        @endif
                    </div>
                </div>

                {{-- Messages --}}
                <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 1rem; background: #f0f2f5;">
                    @php $messages = $this->messages; @endphp

                    @if($messages->isEmpty())
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">
                            <div style="text-align: center;">
                                <x-heroicon-o-chat-bubble-left-right style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem; opacity: 0.3;" />
                                <p style="font-size: 0.875rem;">No messages yet. Send the first message below.</p>
                            </div>
                        </div>
                    @else
                        @foreach($messages as $msg)
                            <div style="display: flex; margin-bottom: 0.5rem; {{ $msg->direction === 'outgoing' ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                                <div style="max-width: 70%; padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 0.75rem; {{ $msg->direction === 'outgoing' ? 'background: var(--primary-500); color: white; border-bottom-right-radius: 0;' : 'background: white; color: #111; border-bottom-left-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' }}">
                                    @if($msg->media_url)
                                        @if($msg->type === 'image')
                                            <a href="{{ Str::startsWith($msg->media_url, ['http://', 'https://']) ? $msg->media_url : Storage::disk('public')->url($msg->media_url) }}" target="_blank">
                                                <img src="{{ Str::startsWith($msg->media_url, ['http://', 'https://']) ? $msg->media_url : Storage::disk('public')->url($msg->media_url) }}" alt="{{ $msg->body }}" style="max-width: 100%; border-radius: 0.5rem; margin-bottom: 0.25rem; max-height: 240px;" />
                                            </a>
                                            @if($msg->body && $msg->body !== '[image]')
                                                <p style="margin-top: 0.25rem;">{{ $msg->body }}</p>
                                            @endif
                                        @elseif($msg->type === 'video')
                                            <video controls style="max-width: 100%; border-radius: 0.5rem; margin-bottom: 0.25rem; max-height: 240px;">
                                                <source src="{{ Str::startsWith($msg->media_url, ['http://', 'https://']) ? $msg->media_url : Storage::disk('public')->url($msg->media_url) }}" />
                                                Your browser does not support video.
                                            </video>
                                            @if($msg->body && $msg->body !== '[video]')
                                                <p style="margin-top: 0.25rem;">{{ $msg->body }}</p>
                                            @endif
                                        @elseif($msg->type === 'audio')
                                            <audio controls style="max-width: 100%;">
                                                <source src="{{ Str::startsWith($msg->media_url, ['http://', 'https://']) ? $msg->media_url : Storage::disk('public')->url($msg->media_url) }}" />
                                            </audio>
                                        @elseif($msg->type === 'document')
                                            <a href="{{ Str::startsWith($msg->media_url, ['http://', 'https://']) ? $msg->media_url : Storage::disk('public')->url($msg->media_url) }}" target="_blank"
                                               style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; border-radius: 0.5rem; {{ $msg->direction === 'outgoing' ? 'background: rgba(255,255,255,0.1);' : 'background: #f3f4f6;' }}">
                                                <x-heroicon-o-document-arrow-down style="width: 2rem; height: 2rem; flex-shrink: 0; {{ $msg->direction === 'outgoing' ? 'color: rgba(255,255,255,0.8);' : 'color: #6b7280;' }}" />
                                                <div style="min-width: 0; flex: 1;">
                                                    <p style="font-size: 0.75rem; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $msg->body ?: 'Document' }}</p>
                                                    <p style="font-size: 10px; {{ $msg->direction === 'outgoing' ? 'color: rgba(255,255,255,0.6);' : 'color: #9ca3af;' }}">Tap to download</p>
                                                </div>
                                            </a>
                                        @else
                                            <p>{{ $msg->body }}</p>
                                        @endif
                                    @else
                                        <p>{{ $msg->body }}</p>
                                    @endif
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; margin-top: 0.25rem;">
                                        <span style="font-size: 10px; opacity: 0.7;">
                                            {{ $msg->created_at->format('h:i A') }}
                                        </span>
                                        @if($msg->direction === 'outgoing')
                                            @if($msg->status === 'read')
                                                <svg style="width: 0.75rem; height: 0.75rem; opacity: 0.7;" fill="currentColor" viewBox="0 0 16 16"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 2.354 7.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/><path d="M15.354 4.354a.5.5 0 0 0-.708-.708L8 10.293l-.146-.147a.5.5 0 1 0-.708.708l.5.5a.5.5 0 0 0 .708 0l7-7z"/></svg>
                                            @elseif($msg->status === 'delivered')
                                                <svg style="width: 0.75rem; height: 0.75rem; opacity: 0.5;" fill="currentColor" viewBox="0 0 16 16"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 2.354 7.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/><path d="M15.354 4.354a.5.5 0 0 0-.708-.708L8 10.293l-.146-.147a.5.5 0 1 0-.708.708l.5.5a.5.5 0 0 0 .708 0l7-7z"/></svg>
                                            @elseif($msg->status === 'sent')
                                                <svg style="width: 0.75rem; height: 0.75rem; opacity: 0.5;" fill="currentColor" viewBox="0 0 16 16"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 2.354 7.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/></svg>
                                            @elseif($msg->status === 'failed')
                                                <x-heroicon-m-exclamation-circle style="width: 0.75rem; height: 0.75rem; color: #fca5a5;" />
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Input --}}
                <div style="border-top: 1px solid #e5e7eb; padding: 0.75rem;">
                    {{-- File preview --}}
                    @if($attachment)
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding: 0.5rem; background: #f3f4f6; border-radius: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; color: #6b7280; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                            </svg>
                            <span style="font-size: 0.75rem; color: #374151; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $attachment->getClientOriginalName() }}</span>
                            <button wire:click="removeAttachment" type="button" style="color: #ef4444; cursor: pointer; border: none; background: none; padding: 0.25rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                    <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
                        {{-- Attachment button --}}
                        <label style="cursor: pointer; padding: 0.5rem; border-radius: 0.5rem; color: #6b7280; display: flex; align-items: center;" title="Attach file">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                            </svg>
                            <input type="file" wire:model="attachment" style="display: none;" accept="image/*,application/pdf,.doc,.docx" />
                        </label>
                        <div style="flex: 1;">
                            <textarea
                                wire:model="messageText"
                                rows="2"
                                placeholder="Type your message..."
                                style="display: block; width: 100%; border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; font-size: 0.875rem; resize: none; color: #111; background: #fff;"
                                wire:keydown.ctrl.enter="sendMessage"
                            ></textarea>
                        </div>
                        <x-filament::button
                            wire:click="sendMessage"
                            color="success"
                            icon="heroicon-m-paper-airplane"
                        >
                            Send
                        </x-filament::button>
                    </div>
                    <p style="margin-top: 0.25rem; font-size: 0.75rem; color: #9ca3af;">Ctrl+Enter to send via WhatsApp Cloud API</p>
                </div>
            @else
                <div style="display: flex; flex: 1; align-items: center; justify-content: center; height: 100%;">
                    <div style="text-align: center; color: #9ca3af;">
                        <x-heroicon-o-chat-bubble-left-right style="width: 4rem; height: 4rem; margin: 0 auto 1rem; opacity: 0.2;" />
                        <p style="font-size: 1.125rem; font-weight: 500;">Select a conversation</p>
                        <p style="font-size: 0.875rem; margin-top: 0.25rem;">Choose from the chat list on the left.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .wa-back-btn { display: block !important; }
        }
    </style>

    @script
    <script>
        function scrollChat() {
            const el = document.getElementById('chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
        }
        Livewire.hook('morph.updated', () => scrollChat());
        scrollChat();
    </script>
    @endscript
</x-filament-panels::page>
