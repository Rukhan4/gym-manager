<script setup>
import { ref, watchEffect } from "vue";
import { router, Link, usePage } from "@inertiajs/vue3";

const props = defineProps({ members: Array, reviews: Array, requiresAuth: Boolean });

const members = ref(props.members || []);
const reviews = ref(props.reviews || []);
const requiresAuth = props.requiresAuth ?? false;

// Login form
const loginForm = ref({ username: "", password: "" });
const loginError = ref("");
const page = usePage();

// show server flash error (e.g. invalid credentials) in the login form
watchEffect(() => {
  const err = page.props?.flash?.error;
  loginError.value = err || "";
});

const login = () => {
  router.post("/admin/login", loginForm.value, {
    // don't force a second client-side GET; let Inertia follow server redirect and supply flash
    onError: (errors) => {
      try {
        // direct message returned from server: { message: '...' }
        if (errors && typeof errors === "object" && errors.message) {
          loginError.value = errors.message;
          return;
        }

        // axios-like shape
        if (errors?.response?.data?.message) {
          loginError.value = errors.response.data.message;
          return;
        }

        // validation-style object { field: ['msg'] }
        if (errors && typeof errors === "object") {
          const first = Object.values(errors)[0];
          loginError.value = Array.isArray(first) ? first.join("\n") : first;
          return;
        }

        loginError.value = "Invalid credentials";
      } catch (e) {
        console.error("Error parsing login error", e, errors);
        loginError.value = "Invalid credentials";
      }
    },
  });
};

const logout = () => {
  router.post("/admin/logout", {}, { onSuccess: () => router.get("/admin") });
};

// Member edit/delete
const showEditModal = ref(false);
const editMember = ref({});

const openEditModal = (m) => {
  editMember.value = { ...m };
  showEditModal.value = true;
};

const updateMember = () => {
  router.put(`/members/${editMember.value.id}`, editMember.value, {
    onSuccess: () => router.get("/admin"),
  });
};

const deleteMember = (id) => {
  if (!confirm("Delete member?")) return;
  router.delete(`/members/${id}`, {
    onSuccess: () => router.get("/admin"),
  });
};

// Review delete (admin only)
const deleteReview = (id) => {
  if (!confirm("Delete review?")) return;
  router.delete(`/reviews/${id}`, {
    onSuccess: () => router.get("/admin"),
  });
};
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-white py-12">
    <div class="container mx-auto px-6">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Admin Console</h1>
        <div class="flex items-center gap-3">
          <Link href="/subscriptions" class="text-sm text-slate-300 hover:text-white">Back</Link>
          <button @click="logout" class="px-3 py-2 bg-red-600 text-white rounded">Logout</button>
        </div>
      </div>

      <div v-if="requiresAuth">
        <div class="max-w-md mx-auto bg-slate-800 rounded p-6 border border-slate-700">
          <h2 class="text-xl font-semibold mb-4">Admin Login</h2>
          <div class="space-y-4">
            <div class="flex flex-col gap-4 font-semibold">Username</div>
            <input
              v-model="loginForm.username"
              placeholder="Username"
              class="w-full px-4 py-2 rounded bg-slate-700 border border-slate-600" />
            <div class="flex flex-col gap-4 font-semibold">Password</div>
            <input
              v-model="loginForm.password"
              type="password"
              placeholder="Password"
              class="w-full px-4 py-2 rounded bg-slate-700 border border-slate-600" />
            <div class="flex items-center justify-between">
              <button @click="login" class="px-4 py-2 bg-emerald-500 rounded">Login</button>
              <div class="text-sm text-red-400">{{ loginError }}</div>
            </div>
          </div>
        </div>
      </div>

      <div v-else>
        <!-- Members Management -->
        <div class="mb-12 bg-slate-800 rounded p-6 border border-slate-700">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Members</h2>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="text-slate-300 text-sm">
                  <th class="p-3">Name</th>
                  <th class="p-3">Email</th>
                  <th class="p-3">Plan</th>
                  <th class="p-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="m in members" :key="m.id" class="border-t border-slate-700">
                  <td class="p-3">{{ m.name }}</td>
                  <td class="p-3">{{ m.email }}</td>
                  <td class="p-3">{{ m.subscription_id }}</td>
                  <td class="p-3 text-center">
                    <button @click="openEditModal(m)" class="px-3 py-1 bg-emerald-600 rounded mr-2">
                      Edit
                    </button>
                    <button @click="deleteMember(m.id)" class="px-3 py-1 bg-red-600 rounded">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Reviews Management -->
        <div class="bg-slate-800 rounded p-6 border border-slate-700">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Reviews</h2>
          </div>

          <div class="space-y-4">
            <div v-for="r in reviews" :key="r.id" class="bg-slate-900/30 p-4 rounded">
              <div class="flex items-start justify-between">
                <div>
                  <div class="font-semibold">
                    {{ r.name }} <span class="text-sm text-slate-400">({{ r.email }})</span>
                  </div>
                  <div class="text-sm text-slate-300">Rating: {{ r.rating }} / 5</div>
                  <div class="text-slate-300 mt-2">{{ r.comment }}</div>
                </div>
                <div class="ml-4">
                  <button @click="deleteReview(r.id)" class="px-3 py-1 bg-red-600 rounded">
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
      <div class="w-full max-w-lg bg-slate-800 rounded p-6 border border-slate-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Edit Member</h3>
          <button @click="showEditModal = false" class="text-slate-300">Close</button>
        </div>
        <div class="space-y-4">
          <input v-model="editMember.name" class="w-full px-4 py-2 bg-slate-700 rounded" />
          <input v-model="editMember.email" class="w-full px-4 py-2 bg-slate-700 rounded" />
          <input
            v-model="editMember.subscription_id"
            class="w-full px-4 py-2 bg-slate-700 rounded" />
          <div class="flex gap-3 justify-end">
            <button @click="showEditModal = false" class="px-4 py-2 bg-slate-600 rounded">
              Cancel
            </button>
            <button @click="updateMember" class="px-4 py-2 bg-emerald-500 rounded">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
