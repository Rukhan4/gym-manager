<script setup>
import { ref, watch, watchEffect } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";

const props = defineProps({
  subscriptions: Array,
  members: Array,
});

// Remove pop ups

const page = usePage();
const showToast = ref(false);
// Keep track of the timeout so repeated flashes don't stack
let toastTimeout = null;

watchEffect(() => {
  const message = page.props?.flash?.success;
  if (message) {
    showToast.value = true;
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      showToast.value = false;
      toastTimeout = null;
    }, 3000); // hide after 3s
  }
});

// Add Member model
const newMember = ref({
  name: "",
  email: "",
  subscription_id: "",
});

const addMember = () => {
  router.post("/members", newMember.value, {
    onSuccess: () => {
      newMember.value = {
        name: "",
        email: "",
        subscription_id: "",
      };
    },
  });
};

// Edit Member modal
const showEditModal = ref(false);
const editMember = ref({});

const openEditModal = (member) => {
  editMember.value = { ...member };
  showEditModal.value = true;
};

const updateMember = () => {
  router.put(`/members/${editMember.value.id}`, editMember.value, {
    onSuccess: () => (showEditModal.value = false),
  });
};

// Delete Member
const deleteMember = (id) => {
  router.delete(`/members/${id}`);
};

// Animate modal pop up
</script>

<template>
  <div
    class="min-h-screen flex flex-col bg-gradient-to-br from-primary/5 via-base-100 to-primary/5 font-serif">
    <!-- Header -->
    <header class="w-full bg-base-100/80 border-b border-base-300 shadow-sm backdrop-blur-sm">
      <div class="container mx-auto py-6 px-4">
        <h1 class="text-4xl font-serif text-center text-primary">Gym Membership Manager</h1>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 container mx-auto p-6 md:p-10">
      <div
        class="w-full max-w-4xl mx-auto space-y-12 bg-base-100/95 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-base-200">
        <!-- Subscription Plans -->
        <section>
          <h2 class="text-2xl font-semibold mb-4 text-center">Subscription Plans</h2>

          <div class="flex justify-center">
            <table
              class="table table-zebra table-auto border bg-base-100 rounded-xl shadow text-center">
              <thead class="bg-base-200">
                <tr>
                  <th class="px-6 py-3">Name</th>
                  <th class="px-6 py-3">Days</th>
                  <th class="px-6 py-3">Price ($)</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="s in props.subscriptions"
                  :key="s.id"
                  class="hover:bg-base-300 transition">
                  <td class="px-6 py-3">
                    {{ s.name }}
                  </td>
                  <td class="px-6 py-3">
                    {{ s.duration_days }}
                  </td>
                  <td class="px-6 py-3">
                    {{ s.price }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Add Member Form -->
        <section>
          <h2 class="text-2xl font-semibold mb-4" id="center-text">Add Member</h2>
          <div class="card bg-base-100 shadow p-6 space-y-4">
            <input
              v-model="newMember.name"
              class="input input-bordered w-full"
              placeholder="Full Name" />
            <input
              v-model="newMember.email"
              class="input input-bordered w-full"
              placeholder="Email Address" />

            <select
              v-model="newMember.subscription_id"
              class="select select-bordered w-full cursor-pointer">
              <option disabled value="">Select Subscription</option>
              <option v-for="s in props.subscriptions" :value="s.id" :key="s.id">
                {{ s.name }}
              </option>
            </select>

            <button
              class="btn btn-primary w-full btn-lg font-semibold hover:scale-105 active:scale-95 transition border-2 border-primary-700 rounded-xl"
              @click="addMember"
              type="button">
              Add
            </button>
          </div>
        </section>

        <!-- Members List -->
        <section>
          <h2 class="text-2xl font-semibold mb-6 text-center">Members</h2>

          <div class="overflow-x-auto flex justify-center">
            <table class="table-auto w-full max-w-4xl bg-base-100 rounded-xl shadow-lg">
              <thead>
                <tr class="bg-primary text-primary-content text-lg">
                  <th class="p-3 text-center">Name</th>
                  <th class="p-3 text-center">Email</th>
                  <th class="p-3 text-center">Subscription</th>
                  <th class="p-3 text-center">Actions</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="m in props.members" :key="m.id" class="hover:bg-base-200 transition">
                  <td class="py-3 text-center font-medium">
                    {{ m.name }}
                  </td>
                  <td class="py-3 text-center">
                    {{ m.email }}
                  </td>
                  <td class="py-3 text-center font-semibold">
                    {{ props.subscriptions.find((s) => s.id === m.subscription_id)?.name }}
                  </td>
                  <td class="py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button
                        class="btn btn-sm hover:bg-sky-200 text-sky-700 border-sky-200 hover:border-sky-300 gap-1"
                        @click="openEditModal(m)">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
                          viewBox="0 0 20 20"
                          fill="currentColor">
                          <path
                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        <span class="hidden sm:inline">Edit</span>
                      </button>
                      <button
                        class="btn btn-sm hover:bg-red-100 text-red-600 border-red-100 hover:border-red-200 gap-1"
                        @click="deleteMember(m.id)">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
                          viewBox="0 0 20 20"
                          fill="currentColor">
                          <path
                            fill-rule="evenodd"
                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                        </svg>
                        <span class="hidden sm:inline">Delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>

    <!-- Modal pop up for edit -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-3 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-3 scale-95">
      <div
        v-if="showEditModal"
        class="fixed inset-0 z-50 grid place-items-center"
        @click.self="showEditModal = false"
        role="dialog"
        aria-modal="true">
        <div
          class="w-full max-w-lg bg-white shadow-2xl border border-sky-100 overflow-hidden rounded-2xl transform transition-all duration-200 ease-out hover:scale-[1.01]">
          <!-- Header -->
          <div
            class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-sky-50 to-white border-b border-sky-100">
            <h3 class="text-xl font-bold text-sky-900">Edit Member Details</h3>
            <button
              class="btn btn-circle btn-sm bg-sky-50 hover:bg-sky-100 border-sky-100 hover:border-sky-200 text-sky-600 hover:rotate-90 transition-all"
              aria-label="Close"
              @click="showEditModal = false">
              ✕
            </button>
          </div>

          <!-- Body -->
          <form class="px-8 py-6 space-y-6" @submit.prevent="updateMember">
            <div class="form-control">
              <label class="label">
                <span class="text-base font-semibold text-gray-700">Full Name</span>
              </label>
              <input
                v-model="editMember.name"
                class="input w-full bg-sky-50/50 border-sky-200 hover:border-sky-300 focus:border-sky-400 focus:ring-2 focus:ring-sky-200 transition-colors"
                placeholder="John Doe"
                required />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text text-base font-semibold">Email</span>
              </label>
              <input
                v-model="editMember.email"
                type="email"
                class="input input-bordered w-full bg-base-200/50 hover:bg-base-200 focus:bg-base-100 transition-colors"
                placeholder="email@example.com"
                required />
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text text-base font-semibold">Subscription Plan</span>
              </label>
              <select
                v-model="editMember.subscription_id"
                class="select select-bordered w-full bg-base-200/50 hover:bg-base-200 focus:bg-base-100 transition-colors">
                <option disabled value="">Choose a plan</option>
                <option v-for="s in props.subscriptions" :value="s.id" :key="s.id" class="py-2">
                  {{ s.name }}
                </option>
              </select>
            </div>
          </form>

          <!-- Actions for member table -->
          <div
            class="flex items-center justify-end gap-4 px-8 py-5 bg-gradient-to-r from-sky-50 to-white border-t border-sky-100">
            <button
              type="button"
              class="btn bg-white hover:bg-gray-50 text-gray-600 border-gray-200 hover:border-gray-300"
              @click="showEditModal = false">
              Cancel
            </button>
            <button
              type="submit"
              class="btn bg-sky-500 border-2 rounded-lg text-white hover:border-sky-700 hover:scale-105 active:scale-100 transition-all"
              @click="updateMember">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>

  <!-- Leave Review Button -->
  <div class="flex justify-center mt-10 mb-10">
    <Link
      href="/reviews"
      class="px-8 py-4 bg-yellow-400 hover:bg-yellow-300 text-black font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-yellow-500">
      Leave Review
    </Link>
  </div>

  <!-- Footer -->
  <footer class="w-full bg-base-100/80 border-t border-base-300 shadow-sm backdrop-blur-sm">
    <div class="container mx-auto py-4 px-4">
      <div class="text-center text-base-content/70">
        <p>&copy; 2025 Gym Membership Manager. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Console Toast Notification for add, edit, delete -->
  <transition
    enter-active-class="transition transform ease-out duration-300"
    enter-from-class="opacity-0 -translate-y-3 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition transform ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-2 scale-95">
    <div v-if="showToast" class="fixed top-4 right-4 z-50">
      <div class="max-w-sm w-full">
        <div
          class="alert alert-success shadow-lg bg-success/95 text-success-content backdrop-blur-sm border border-success/20 flex items-start gap-4">
          <div class="flex-1">
            <div class="font-semibold">Success</div>
            <div class="text-sm">{{ page.props.flash.success }}</div>
          </div>
          <button
            type="button"
            class="btn btn-ghost btn-sm"
            aria-label="Close notification"
            @click="showToast = false">
            ✕
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>
