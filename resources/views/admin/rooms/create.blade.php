<x-layouts.admin :title="__('rooms.admin.new_room')">
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('rooms.common.back') }}
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('rooms.admin.new_room') }}</h1>
            <p class="text-sm text-slate-500 mt-1">Add a new meeting room or workspace to the enterprise schedule.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">
                <div class="font-semibold mb-1">Please fix the following errors:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name EN -->
                <div>
                    <label for="name_en" class="block text-sm font-semibold text-slate-700 mb-1.5">Room Name (English) *</label>
                    <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}" placeholder="e.g. Boardroom Apollo" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Name SR -->
                <div>
                    <label for="name_sr" class="block text-sm font-semibold text-slate-700 mb-1.5">Room Name (Serbian Latin) *</label>
                    <input type="text" id="name_sr" name="name_sr" value="{{ old('name_sr') }}" placeholder="npr. Sala za sastanke Apollo" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-slate-700 mb-1.5">Location / Floor *</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="e.g. 2nd Floor, East Wing" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-semibold text-slate-700 mb-1.5">Maximum Capacity *</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 8) }}" min="1" max="200" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Color Hex -->
                <div>
                    <label for="color_hex" class="block text-sm font-semibold text-slate-700 mb-1.5">Calendar & Tag Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="color_picker" value="{{ old('color_hex', '#4F46E5') }}"
                               oninput="document.getElementById('color_hex').value = this.value"
                               class="w-10 h-10 rounded-xl border border-slate-200 cursor-pointer p-0.5">
                        <input type="text" id="color_hex" name="color_hex" value="{{ old('color_hex', '#4F46E5') }}"
                               oninput="document.getElementById('color_picker').value = this.value"
                               class="flex-1 rounded-xl border-slate-200 text-sm font-mono focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                    </div>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-slate-700 mb-1.5">Display Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 10) }}" min="0"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>

                <!-- Description EN -->
                <div class="md:col-span-2">
                    <label for="description_en" class="block text-sm font-semibold text-slate-700 mb-1.5">Description (English)</label>
                    <textarea id="description_en" name="description_en" rows="2"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('description_en') }}</textarea>
                </div>

                <!-- Description SR -->
                <div class="md:col-span-2">
                    <label for="description_sr" class="block text-sm font-semibold text-slate-700 mb-1.5">Description (Serbian Latin)</label>
                    <textarea id="description_sr" name="description_sr" rows="2"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('description_sr') }}</textarea>
                </div>

                <!-- Photo Upload -->
                <div class="md:col-span-2">
                    <label for="photo" class="block text-sm font-semibold text-slate-700 mb-1.5">Room Photo (Optional, max 2MB JPG/PNG/WebP)</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <!-- Equipment Options -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Available Equipment & Amenities</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ($standardEquipments as $idx => $eq)
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:bg-slate-50/80 cursor-pointer text-xs">
                                <input type="checkbox" name="equipments[{{ $idx }}][code]" value="{{ $eq['code'] }}" class="rounded text-indigo-600 focus:ring-indigo-500 mt-0.5">
                                <input type="hidden" name="equipments[{{ $idx }}][label_en]" value="{{ $eq['en'] }}">
                                <input type="hidden" name="equipments[{{ $idx }}][label_sr]" value="{{ $eq['sr'] }}">
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $eq['en'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $eq['sr'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Active Toggle -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-slate-800">Room is active and bookable on public portal</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.rooms.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    {{ __('rooms.common.cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                    {{ __('rooms.common.save') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
