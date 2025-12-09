<script setup>
import { ref, watch, watchEffect, onMounted, onBeforeUnmount, computed } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";

const props = defineProps({
  subscriptions: Array,
  members: Array,
});

const page = usePage();
const showToast = ref(false);
let toastTimeout = null;
const toastMessage = ref("");

watchEffect(() => {
  const message = page.props?.flash?.success;
  if (message) {
    toastMessage.value = message;
    showToast.value = true;
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      showToast.value = false;
      toastTimeout = null;
      toastMessage.value = "";
    }, 3000);
  }
});

// Initialize member
const newMember = ref({
  name: "",
  email: "",
  subscription_id: "",
});

// Carousel features
const carouselPause = ref(false);
const currentFeature = ref(0);
// animation state for simpler carousel
const animating = ref(false);
const transitionMs = 500; // transition duration in ms

// Feature data
const features = [
  {
    id: 1,
    title: "Individualized Classes",
    subtitle: "HIIT • Yoga • Pilates",
    details:
      "Tailored group classes for all fitness levels. Whether you're looking for high-intensity interval training, mindful yoga sessions, or core-strengthening pilates, we have instructors who guide you through every step.",
  },
  {
    id: 2,
    title: "Extra Amenities",
    subtitle: "Sauna & Smoothie Bar",
    details:
      "Relax and recover in our state-of-the-art sauna, then refuel at our premium smoothie bar with fresh, nutritious blends made daily to support your fitness goals.",
  },
  {
    id: 3,
    title: "Friendly Staff",
    subtitle: "Welcoming trainers and support",
    details:
      "Our team genuinely cares about your success. We're here to answer questions, provide modifications, and create an encouraging environment where everyone feels welcome.",
  },
  {
    id: 4,
    title: "State of the Art Equipment",
    subtitle: "Modern machines and free weights",
    details:
      "We've invested in premium equipment including the latest cardio machines, strength training rigs, and functional fitness tools to support every type of workout.",
  },
  {
    id: 5,
    title: "Certified Trainers",
    subtitle: "ISSA Certified with PT experience",
    details:
      "All our trainers are ISSA certified with extensive personal training experience. They develop customized workout plans and form coaching to help you reach your goals safely and effectively.",
  },
  {
    id: 6,
    title: "Community Driven",
    subtitle: "More than just a gym",
    details:
      "We believe fitness is better together. From group challenges to member events, we foster a supportive community where members motivate each other and celebrate victories together.",
  },
];

let carouselTimer = null;
// simple auto-advance helpers
const startCarousel = () => {
  if (carouselTimer) return;
  carouselTimer = setInterval(() => {
    if (!carouselPause.value) goNext();
  }, 4000);
};
const stopCarousel = () => {
  if (carouselTimer) {
    clearInterval(carouselTimer);
    carouselTimer = null;
  }
};

// computed indices and objects for the three visible cards
const displayedIndices = computed(() => {
  const n = features.length;
  const center = currentFeature.value;
  const left = (center - 1 + n) % n;
  const right = (center + 1) % n;
  return [left, center, right];
});
const displayedLeft = computed(
  () => features[displayedIndices.value[0]] || { title: "", subtitle: "", details: "" }
);
const displayedCenter = computed(
  () => features[displayedIndices.value[1]] || { title: "", subtitle: "", details: "" }
);
const displayedRight = computed(
  () => features[displayedIndices.value[2]] || { title: "", subtitle: "", details: "" }
);
const goNext = () => {
  if (animating.value) return;
  animating.value = true;
  currentFeature.value = (currentFeature.value + 1) % features.length;
  setTimeout(() => (animating.value = false), transitionMs);
};

const goPrev = () => {
  if (animating.value) return;
  animating.value = true;
  currentFeature.value = (currentFeature.value - 1 + features.length) % features.length;
  setTimeout(() => (animating.value = false), transitionMs);
};

// Animate to a specific index (dot clicks). For adjacent moves use goNext/goPrev for animation; for further distance, step repeatedly.
const goTo = (target) => {
  if (animating.value) return;
  const n = features.length;
  const cur = currentFeature.value;
  if (target === cur) return;
  // compute shortest direction
  const forwardDist = (target - cur + n) % n;
  const backwardDist = (cur - target + n) % n;
  if (forwardDist <= backwardDist) {
    // move forward forwardDist times (chain)
    const step = () => {
      if (currentFeature.value === target) return;
      goNext();
      setTimeout(() => {
        step();
      }, transitionMs + 30);
    };
    step();
  } else {
    const step = () => {
      if (currentFeature.value === target) return;
      goPrev();
      setTimeout(() => {
        step();
      }, transitionMs + 30);
    };
    step();
  }
};

onMounted(() => startCarousel());
onBeforeUnmount(() => stopCarousel());

// Add new Member
const addMember = () => {
  router.post("/members", newMember.value, {
    onSuccess: () => {
      newMember.value = {
        name: "",
        email: "",
        subscription_id: "",
      };
      // show immediate client-side toast with instruction
      toastMessage.value =
        "Member added! Please visit our branch to complete payment and registration.";
      showToast.value = true;
      if (toastTimeout) clearTimeout(toastTimeout);
      toastTimeout = setTimeout(() => {
        showToast.value = false;
        toastTimeout = null;
        toastMessage.value = "";
      }, 5000);
    },
  });
};

const showEditModal = ref(false);
const editMember = ref({});

// Submit to admin page
const updateMember = () => {
  router.put(`/members/${editMember.value.id}`, editMember.value, {
    onSuccess: () => (showEditModal.value = false),
  });
};
</script>

<template>
  <div
    class="min-h-screen w-full flex flex-col bg-gradient-to-br from-emerald-700 via-slate-950 to-cyan-700 text-white">
    <!-- Navigation -->
    <nav
      class="fixed top-0 left-0 right-0 z-40 bg-transparent backdrop-blur-lg border-b border-slate-800/30">
      <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <svg
              class="w-10 h-10 text-emerald-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span class="text-2xl font-bold text-white">MOE GYMS</span>
          </div>
          <div class="hidden md:flex items-center gap-8">
            <a
              href="#home"
              class="text-slate-300 hover:text-emerald-400 transition-colors font-medium"
              >Home</a
            >
            <a
              href="#features"
              class="text-slate-300 hover:text-emerald-400 transition-colors font-medium"
              >Features</a
            >
            <a
              href="#plans"
              class="text-slate-300 hover:text-emerald-400 transition-colors font-medium"
              >Plans</a
            >
            <a
              href="#facility"
              class="text-slate-300 hover:text-emerald-400 transition-colors font-medium"
              >Facility</a
            >
            <a
              href="#join"
              class="text-slate-300 hover:text-emerald-400 transition-colors font-medium"
              >Join Now</a
            >
            <Link
              href="/admin"
              class="text-slate-300 hover:text-emerald-400 transition-colors font-medium"
              >Admin</Link
            >
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section
      id="home"
      class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
      <!-- Background -->
      <div
        class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-950 to-cyan-600/20"></div>
      <div
        class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjAzIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-50"></div>

      <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-6xl md:text-8xl font-black text-white mb-6 leading-tight">
            WELCOME TO<br />
            <span
              class="bg-gradient-to-r from-emerald-400 to-cyan-400 text-transparent bg-clip-text"
              >MOE GYMS</span
            >
          </h1>
          <p class="text-2xl md:text-3xl text-slate-300 font-light mb-4">
            GET THE BODY YOU WANT AT THE GYM YOU LOVE
          </p>
          <p class="text-lg text-slate-400 mb-12 max-w-3xl mx-auto leading-relaxed">
            Come and experience an Ultra Modern, State of the Art Gym Facility with three levels
            that caters to all your fitness needs. Whatever type of fitness workout you are looking
            for we got it. We are open 24/7 and welcome persons of all ages, gender and fitness
            level.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="#join"
              class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white font-bold rounded-xl shadow-lg hover:shadow-emerald-500/50 transform hover:scale-105 transition-all text-lg">
              Join Now
            </a>
            <a
              href="#plans"
              class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl border-2 border-slate-700 hover:border-emerald-500 transform hover:scale-105 transition-all text-lg">
              View Plans
            </a>
          </div>
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-900/50">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Why Choose MOE Gyms</h2>
          <p class="text-xl text-slate-400">Everything you need for your fitness journey</p>
        </div>

        <div class="max-w-7xl mx-auto px-4">
          <div
            class="relative flex items-stretch justify-between gap-8 py-8 min-h-80"
            @mouseenter="carouselPause = true"
            @mouseleave="carouselPause = false">
            <!-- Left arrow -->
            <button
              @click="goPrev"
              class="hidden lg:flex items-center justify-center w-14 h-14 rounded-full bg-emerald-500/20 hover:bg-emerald-500/40 border border-emerald-500/50 text-emerald-300 hover:text-emerald-200 transition-all duration-300 flex-shrink-0 self-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            <!-- Simple 3-card carousel -->
            <div class="flex-1 flex items-center justify-center gap-6">
              <!-- Left -->
              <div
                class="w-72 p-6 rounded-2xl bg-slate-800/60 border border-slate-700 min-h-72 transition-all duration-500 transform"
                :class="{ 'opacity-80 scale-95': true }">
                <h4 class="text-lg font-semibold text-emerald-300 line-clamp-2">
                  {{ displayedLeft.title }}
                </h4>
                <p class="text-sm text-slate-300 mt-2 line-clamp-2">{{ displayedLeft.subtitle }}</p>
              </div>

              <!-- Center -->
              <div
                class="w-96 p-8 rounded-3xl bg-gradient-to-br from-slate-800/80 to-slate-900 border-2 border-emerald-500/50 shadow-2xl shadow-emerald-500/20 min-h-96 transition-all duration-500 transform"
                :class="{ 'scale-105': !animating, 'opacity-100': true }">
                <h3 class="text-2xl font-bold text-white">{{ displayedCenter.title }}</h3>
                <p class="text-sm text-emerald-300 mt-2 font-semibold">
                  {{ displayedCenter.subtitle }}
                </p>
                <p class="text-slate-300 mt-4 text-sm leading-relaxed">
                  {{ displayedCenter.details }}
                </p>
              </div>

              <!-- Right -->
              <div
                class="w-72 p-6 rounded-2xl bg-slate-800/60 border border-slate-700 min-h-72 transition-all duration-500 transform"
                :class="{ 'opacity-80 scale-95': true }">
                <h4 class="text-lg font-semibold text-emerald-300 line-clamp-2">
                  {{ displayedRight.title }}
                </h4>
                <p class="text-sm text-slate-300 mt-2 line-clamp-2">
                  {{ displayedRight.subtitle }}
                </p>
              </div>
            </div>

            <!-- Right arrow -->
            <button
              @click="goNext"
              class="hidden lg:flex items-center justify-center w-14 h-14 rounded-full bg-emerald-500/20 hover:bg-emerald-500/40 border border-emerald-500/50 text-emerald-300 hover:text-emerald-200 transition-all duration-300 flex-shrink-0 self-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
          <!-- Dots -->
          <div class="flex items-center justify-center gap-2 mt-8">
            <button
              v-for="(f, i) in features"
              :key="f.id"
              @click="currentFeature = i"
              :class="{
                'w-3 h-3 rounded-full transition-all duration-300': true,
                'bg-emerald-400 w-8': currentFeature === i,
                'bg-slate-700': currentFeature !== i,
              }"></button>
          </div>
        </div>
      </div>
    </section>

    <!-- Facility Section -->
    <section id="facility" class="py-24 bg-slate-950">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">OUR FACILITY</h2>
          <p class="text-xl text-emerald-400 font-semibold mb-4">
            3 LEVELS OF TRAINING SPACE AT OUR POS BRANCH
          </p>
          <p class="text-lg text-slate-400 max-w-3xl mx-auto">
            Our members enjoy a facility that is designed to cater to their comfort and fitness
            goals while we maintain international standards.
          </p>
        </div>

        <div class="grid grid-cols-3 gap-6 max-w-7xl mx-auto">
          <div
            class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 border border-slate-700 hover:border-emerald-500 transition-all transform hover:scale-105">
            <div class="text-4xl mb-4"></div>
            <h3 class="text-xl font-bold text-white mb-2">Weight Training</h3>
            <p class="text-slate-400">State-of-the-art weight equipment</p>
          </div>
          <div
            class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 border border-slate-700 hover:border-emerald-500 transition-all transform hover:scale-105">
            <div class="text-4xl mb-4"></div>
            <h3 class="text-xl font-bold text-white mb-2">Cardio Zone</h3>
            <p class="text-slate-400">Premium cardio machines</p>
          </div>
          <div
            class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 border border-slate-700 hover:border-emerald-500 transition-all transform hover:scale-105">
            <div class="text-4xl mb-4"></div>
            <h3 class="text-xl font-bold text-white mb-2">Yoga Zone</h3>
            <p class="text-slate-400">Peaceful practice space</p>
          </div>
        </div>

        <div class="mt-12 text-center">
          <div
            class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-500/20 border border-emerald-500/30 rounded-full">
            <svg
              class="w-5 h-5 text-emerald-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-emerald-400 font-bold text-lg">OPEN 24/7</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Membership Plans -->
    <section id="plans" class="py-24 bg-slate-900/50">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Membership Plans</h2>
          <p class="text-xl text-slate-400">Choose the perfect plan for your fitness journey</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div v-for="s in props.subscriptions" :key="s.id" class="group relative">
            <div
              class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-cyan-500 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity"></div>
            <div
              class="relative bg-slate-800/50 backdrop-blur-sm border border-slate-700 hover:border-emerald-500 rounded-2xl p-8 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl hover:shadow-emerald-500/20 h-full flex flex-col">
              <div class="text-center flex-1">
                <h3 class="text-2xl font-bold text-white mb-4">{{ s.name }}</h3>
                <div class="my-8">
                  <span class="text-6xl font-bold text-emerald-400">${{ s.price }}</span>
                </div>
                <div
                  class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700/50 rounded-full mb-6">
                  <svg
                    class="w-5 h-5 text-emerald-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="text-slate-300 font-medium">{{ s.duration_days }} Days Access</span>
                </div>
              </div>
              <div class="mt-6">
                <a
                  href="#join"
                  class="block w-full py-3 bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white font-bold rounded-xl text-center transition-all transform hover:scale-105">
                  Select Plan
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Join Section -->
    <section id="join" class="py-24 bg-slate-950">
      <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Join MOE Gyms Today</h2>
            <p class="text-xl text-slate-400">Start your fitness journey with us</p>
          </div>

          <!-- Add Member Form -->
          <div
            class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 md:p-12 border border-slate-700 shadow-2xl">
            <div class="text-center mb-8">
              <div
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-emerald-500 to-cyan-500 rounded-2xl mb-6">
                <svg
                  class="w-10 h-10 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>
              <h3 class="text-3xl font-bold text-white mb-2">Register Now</h3>
              <p class="text-slate-400 text-lg">Fill out the form below to become a member</p>
            </div>

            <div class="space-y-6">
              <div>
                <label class="block text-sm font-semibold text-slate-300 mb-3">Full Name</label>
                <input
                  v-model="newMember.name"
                  class="w-full px-5 py-4 bg-slate-700/50 border border-slate-600 rounded-xl text-white text-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                  placeholder="Enter your full name" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-300 mb-3">Email Address</label>
                <input
                  v-model="newMember.email"
                  type="email"
                  class="w-full px-5 py-4 bg-slate-700/50 border border-slate-600 rounded-xl text-white text-lg placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                  placeholder="your.email@example.com" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-300 mb-3"
                  >Select Your Plan</label
                >
                <select
                  v-model="newMember.subscription_id"
                  class="w-full px-5 py-4 bg-slate-700/50 border border-slate-600 rounded-xl text-white text-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all cursor-pointer">
                  <option disabled value="" class="bg-slate-800">
                    Choose your membership plan
                  </option>
                  <option
                    v-for="s in props.subscriptions"
                    :value="s.id"
                    :key="s.id"
                    class="bg-slate-800">
                    {{ s.name }} - ${{ s.price }} ({{ s.duration_days }} days)
                  </option>
                </select>
              </div>

              <button
                @click="addMember"
                type="button"
                class="w-full py-5 bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white text-lg font-bold rounded-xl shadow-lg hover:shadow-emerald-500/50 transform hover:scale-[1.02] active:scale-95 transition-all duration-200">
                Complete Registration
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Review CTA -->
    <div class="text-center mt-16">
      <Link
        href="/reviews"
        class="inline-flex items-center gap-3 px-8 py-4 mb-5 bg-gradient-to-r from-yellow-400 to-orange-400 hover:from-yellow-500 hover:to-orange-500 text-black font-bold rounded-xl shadow-lg hover:shadow-yellow-400/50 transform hover:scale-105 active:scale-95 transition-all duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
        Leave a Review
      </Link>
    </div>

    <!-- Footer -->
    <footer>
      <div class="w-full max-w-7xl mx-auto px-6 py-8">
        <div class="text-center">
          <div class="flex items-center justify-center gap-2 mb-3">
            <svg
              class="w-6 h-6 text-emerald-300"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span class="text-xl font-bold text-white">FitTrack Pro</span>
          </div>
          <p class="text-slate-300 text-sm">&copy; 2025 FitTrack Pro. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>

  <!-- Edit Modal -->
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0">
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
      @click.self="showEditModal = false">
      <div
        class="w-full max-w-lg bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl border border-slate-700 shadow-2xl transform transition-all">
        <div class="p-6 border-b border-slate-700">
          <div class="flex items-center justify-between">
            <h3 class="text-2xl font-bold text-white">Edit Member</h3>
            <button
              @click="showEditModal = false"
              class="p-2 hover:bg-slate-700 rounded-lg transition-colors text-slate-400 hover:text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Full Name</label>
            <input
              v-model="editMember.name"
              class="w-full px-4 py-3 bg-slate-700/50 border border-slate-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Email Address</label>
            <input
              v-model="editMember.email"
              type="email"
              class="w-full px-4 py-3 bg-slate-700/50 border border-slate-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Subscription Plan</label>
            <select
              v-model="editMember.subscription_id"
              class="w-full px-4 py-3 bg-slate-700/50 border border-slate-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all cursor-pointer">
              <option disabled value="" class="bg-slate-800">Choose a plan</option>
              <option
                v-for="s in props.subscriptions"
                :value="s.id"
                :key="s.id"
                class="bg-slate-800">
                {{ s.name }}
              </option>
            </select>
          </div>

          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showEditModal = false"
              class="flex-1 px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-all">
              Cancel
            </button>
            <button
              type="button"
              @click="updateMember"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-emerald-500/50 transition-all">
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>

  <!-- Toast Notification -->
  <transition
    enter-active-class="transition transform ease-out duration-300"
    enter-from-class="opacity-0 translate-x-8"
    enter-to-class="opacity-100 translate-x-0"
    leave-active-class="transition transform ease-in duration-200"
    leave-from-class="opacity-100 translate-x-0"
    leave-to-class="opacity-0 translate-x-8">
    <div v-if="showToast" class="fixed top-6 right-6 z-50">
      <div
        class="bg-gradient-to-r from-emerald-500 to-cyan-500 text-white px-6 py-4 rounded-xl shadow-2xl border border-emerald-400/30 flex items-center gap-4 max-w-md">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="flex-1">
          <div class="font-bold">Success!</div>
          <div class="text-sm text-white/90">{{ toastMessage }}</div>
        </div>
        <button
          @click="showToast = false"
          class="p-1 hover:bg-white/20 rounded-lg transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </transition>
</template>
