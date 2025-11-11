<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    rating: "",
    comment: "",
});

const props = defineProps({
    reviews: Array,
});
</script>

<template>
    <div class="p-6 max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Customer Reviews</h1>

        <form @submit.prevent="form.post(route('reviews.store'))" class="space-y-4 mb-8">
            <input
                v-model="form.name"
                type="text"
                placeholder="Your Name"
                class="border p-2 w-full" />

            <select v-model="form.rating" class="border p-2 w-full">
                <option value="">Select Rating</option>
                <option v-for="num in [1, 2, 3, 4, 5]" :key="num" :value="num">{{ num }}</option>
            </select>

            <textarea
                v-model="form.comment"
                placeholder="Write your review"
                class="border p-2 w-full h-24"></textarea>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Submit Review
            </button>
        </form>

        <h2 class="text-xl font-semibold mb-4">Recent Reviews</h2>

        <div v-for="review in props.reviews" :key="review.id" class="border-b py-3">
            <p class="font-bold">{{ review.name }} ({{ review.rating }}/5)</p>
            <p>{{ review.comment }}</p>
        </div>
    </div>
</template>
