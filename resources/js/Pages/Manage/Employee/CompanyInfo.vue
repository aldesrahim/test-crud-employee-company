<script setup>
defineProps({
    company: {
        type: Object,
        required: true,
        default: () => ({
            name: 'N/A',
            email: 'N/A',
            website: 'N/A',
            logo_url: null,
        }),
    },
})
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Name -->
        <div class="flex flex-col">
            <span class="text-gray-700 font-medium">Name</span>
            <span class="text-gray-900">{{ company.name }}</span>
        </div>

        <div class="flex flex-col">
            <span class="text-gray-700 font-medium">Email</span>
            <span class="text-gray-900">{{ company.email }}</span>
        </div>

        <div class="flex flex-col">
            <span class="text-gray-700 font-medium">Website</span>
            <a
                :href="company.website"
                target="_blank"
                rel="noopener noreferrer"
                class="text-indigo-600 hover:underline"
            >
                {{ company.website || 'N/A' }}
            </a>
        </div>

        <div class="flex flex-col sm:col-span-2">
            <span class="text-gray-700 font-medium">Logo</span>
            <div class="mt-2">
                <img
                    v-if="company.logo_url"
                    :src="company.logo_url"
                    alt="Company Logo"
                    class="h-16 w-16 object-cover rounded-full border"
                />
                <span v-else class="text-gray-500">No logo available</span>
            </div>
        </div>
    </div>
</template>

