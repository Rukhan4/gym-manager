<script setup>
import { ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import { CheckCircleIcon } from "lucide-vue-next";
import leoProfanity from "leo-profanity";

leoProfanity.loadDictionary(); // load default bad words

const props = defineProps({
  reviews: Array,
});

const newReview = ref({
  name: "",
  email: "",
  rating: 5,
  comment: "",
});

const submitReview = () => {
  if (leoProfanity.check(newReview.value.comment)) {
    alert("Please remove inappropriate language from your comment.");
    return;
  }

  router.post("/reviews", newReview.value, {
    onSuccess: () => {
      newReview.value = { name: "", email: "", rating: 5, comment: "" };
      router.get("/reviews");
    },
    onError: (errors) => {
      console.warn("Validation errors:", errors);
      if (errors && Object.keys(errors).length) {
        const first = Object.values(errors)[0];
        alert(Array.isArray(first) ? first.join("\n") : first);
      }
    },
  });
};

const deleteReview = (id) => {
  router.delete(`/reviews/${id}`, {
    onSuccess: () => router.get("/reviews"),
  });
};

const getRatingStars = (rating) => {
  return Array.from({ length: 5 }, (_, i) => i < rating);
};
</script>

<template>
  <div
    class="min-h-screen flex flex-col bg-gradient-to-br from-primary/5 via-base-100 to-primary/5">
    <!-- Header -->
    <header class="w-full bg-base-100/80 border-b border-base-300 shadow-sm backdrop-blur-sm">
      <div class="container mx-auto py-6 px-4 flex justify-between items-center">
        <h1 class="text-4xl font-bold text-primary">Gym Reviews</h1>
        <Link href="/subscriptions" class="btn btn-ghost cursor-pointer font-serif">← Back</Link>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 container mx-auto p-6 md:p-10">
      <div class="w-full max-w-4xl mx-auto space-y-12">
        <!-- Add Review Form -->
        <section
          class="bg-base-100/95 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-base-200">
          <h2 class="text-2xl font-semibold mb-6 text-center">Leave a Review</h2>

          <form @submit.prevent="submitReview" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="form-control">
                <label class="label"
                  ><span class="text-base font-semibold text-gray-700">Name</span></label
                >
                <input
                  v-model="newReview.name"
                  type="text"
                  class="input w-full bg-sky-50/50 border-sky-200 focus:ring-2 focus:ring-sky-200"
                  required />
              </div>
              <div class="form-control">
                <label class="label"
                  ><span class="text-base font-semibold text-gray-700">Email</span></label
                >
                <input
                  v-model="newReview.email"
                  type="email"
                  class="input w-full bg-sky-50/50 border-sky-200 focus:ring-2 focus:ring-sky-200"
                  required />
              </div>
            </div>

            <div class="form-control">
              <label class="label"
                ><span class="text-base font-semibold text-gray-700">Rating</span></label
              >
              <select
                v-model.number="newReview.rating"
                class="select w-full cursor-pointer bg-sky-50/50 border-sky-200 focus:ring-2 focus:ring-sky-200">
                <option :value="5">★★★★★ - Excellent</option>
                <option :value="4">★★★★☆ - Very Good</option>
                <option :value="3">★★★☆☆ - Good</option>
                <option :value="2">★★☆☆☆ - Fair</option>
                <option :value="1">★☆☆☆☆ - Poor</option>
              </select>
            </div>

            <div class="form-control">
              <label class="label"
                ><span class="text-base font-semibold text-gray-700">Comment</span></label
              >
              <textarea
                v-model="newReview.comment"
                class="textarea w-full bg-sky-50/50 border-sky-200 focus:ring-2 focus:ring-sky-200 resize-none"
                rows="5"
                required></textarea>
            </div>

            <button
              type="submit"
              class="btn w-full bg-sky-500 hover:bg-sky-600 text-white border-sky-600 py-3 text-lg font-semibold">
              Submit Review
            </button>
          </form>
        </section>

        <!-- Reviews List -->
        <section>
          <h2 class="text-2xl font-semibold mb-6 text-center">All Reviews</h2>

          <div v-if="props.reviews && props.reviews.length > 0" class="space-y-4">
            <div
              v-for="review in props.reviews"
              :key="review.id"
              class="bg-base-100/95 backdrop-blur-sm p-6 rounded-2xl shadow-md border border-base-200 hover:shadow-lg transition-all">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold text-gray-800">{{ review.name }}</h3>

                    <span
                      v-if="review.verified"
                      class="flex items-center gap-1 text-green-600 text-sm font-medium">
                      <CheckCircleIcon class="w-4 h-4" /> Verified Member
                    </span>
                  </div>
                  <p class="text-sm text-gray-500">{{ review.email }}</p>
                </div>

                <button
                  @click="deleteReview(review.id)"
                  class="btn btn-sm bg-red-50 hover:bg-red-100 text-red-600 border-red-100">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd" />
                  </svg>
                </button>
              </div>

              <div class="flex items-center gap-1 mb-3">
                <template v-for="(filled, i) in getRatingStars(review.rating)" :key="i">
                  <svg
                    v-if="filled"
                    class="w-6 h-6 text-yellow-400"
                    fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.196-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81H6.81a1 1 0 00.95-.69l1.29-3.967z" />
                  </svg>
                  <svg v-else class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.196-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81H6.81a1 1 0 00.95-.69l1.29-3.967z" />
                  </svg>
                </template>
              </div>

              <p class="text-gray-700">{{ review.comment }}</p>
            </div>
          </div>

          <div
            v-else
            class="bg-base-100/95 backdrop-blur-sm p-8 rounded-2xl shadow-md border text-center">
            <p class="text-gray-500 text-lg">No reviews yet. Be the first to leave one!</p>
          </div>
        </section>
      </div>
    </div>

    <!-- Footer -->
    <footer class="w-full bg-base-100/80 border-t border-base-300 shadow-sm backdrop-blur-sm">
      <div class="container mx-auto py-4 px-4 text-center text-base-content/70">
        <p>&copy; 2025 Gym Membership Manager. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>
