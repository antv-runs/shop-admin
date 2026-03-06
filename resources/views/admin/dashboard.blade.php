@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto">
                <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold">Admin Dashboard</h2>
            <p class="mt-2 text-gray-600">Welcome, {{ auth()->user() ? auth()->user()->name : 'Admin' }}.</p>

            <!-- Tabs -->
            <div class="mt-6">
                <div class="border-b">
                    <nav class="-mb-px flex space-x-8" role="tablist" aria-label="Admin tabs">
                        <button id="tab-content" role="tab" aria-controls="panel-content" aria-selected="true" class="py-2 px-3 border-b-2 border-indigo-500 text-sm font-medium text-indigo-600 cursor-pointer">Learning Content</button>
                        <button id="tab-exercises" role="tab" aria-controls="panel-exercises" aria-selected="false" class="py-2 px-3 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 cursor-pointer">Exercises</button>
                        <button id="tab-other" role="tab" aria-controls="panel-other" aria-selected="false" class="py-2 px-3 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 cursor-pointer">Other</button>
                    </nav>
                </div>

                <div id="panel-content" class="mt-4" role="tabpanel" aria-labelledby="tab-content">
                    <h3 class="text-lg font-semibold">Learning Content</h3>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>CRUD Category</li>
                        <li>CRUD Product</li>
                        <li>Upload image</li>
                        <li>Validation</li>
                    </ul>
                </div>

                <div id="panel-exercises" class="hidden mt-4" role="tabpanel" aria-labelledby="tab-exercises">
                    <h3 class="text-lg font-semibold">Exercises</h3>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>Complete admin: Category management</li>
                        <li>Complete admin: Product management</li>
                        <li>Complete admin: User management</li>
                    </ul>
                </div>

                <div id="panel-other" class="hidden mt-4" role="tabpanel" aria-labelledby="tab-other">
                    <h3 class="text-lg font-semibold">Other</h3>
                    <p class="mt-2 text-gray-700">Additional tools and information will be updated here.</p>
                </div>
            </div>
        </div>

    <script>
        // Enhanced tab switching: manage aria-selected and active classes
        const tabs = Array.from(document.querySelectorAll('[role="tab"]'));
        const panels = Array.from(document.querySelectorAll('[role="tabpanel"]'));

        function activateTab(tab) {
            tabs.forEach(t => {
                t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
                t.classList.remove('border-indigo-500','text-indigo-600');
                t.classList.add('border-transparent','text-gray-500');
            });

            panels.forEach(p => p.classList.add('hidden'));

            tab.classList.add('border-indigo-500','text-indigo-600');
            const panel = document.getElementById(tab.getAttribute('aria-controls'));
            if (panel) panel.classList.remove('hidden');
        }

        tabs.forEach(t => t.addEventListener('click', () => activateTab(t)));

        // ensure first tab active on load
        const defaultTab = document.querySelector('[role="tab"][aria-selected="true"]') || tabs[0];
        if (defaultTab) activateTab(defaultTab);
    </script>
    </div>

    <script>
        // Simple tab switching
        const tabContent = document.getElementById('tab-content');
        const tabExercises = document.getElementById('tab-exercises');
        const tabOther = document.getElementById('tab-other');

        const panelContent = document.getElementById('panel-content');
        const panelExercises = document.getElementById('panel-exercises');
        const panelOther = document.getElementById('panel-other');

        function clearActive() {
            [tabContent, tabExercises, tabOther].forEach(t => {
                t.classList.remove('border-indigo-500','text-indigo-600');
                t.classList.add('border-transparent','text-gray-500');
            });
            [panelContent, panelExercises, panelOther].forEach(p => p.classList.add('hidden'));
        }

        tabContent.addEventListener('click', () => {
            clearActive();
            tabContent.classList.add('border-indigo-500','text-indigo-600');
            panelContent.classList.remove('hidden');
        });

        tabExercises.addEventListener('click', () => {
            clearActive();
            tabExercises.classList.add('border-indigo-500','text-indigo-600');
            panelExercises.classList.remove('hidden');
        });

        tabOther.addEventListener('click', () => {
            clearActive();
            tabOther.classList.add('border-indigo-500','text-indigo-600');
            panelOther.classList.remove('hidden');
        });
    </script>
@endsection
