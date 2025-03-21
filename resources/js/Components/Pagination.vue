<template>
  <div class="flex justify-center space-x-2">
    <template v-for="link in links" :key="link.url">
      <Link
        v-if="link.url"
        :href="link.url"
        class="px-4 py-2 border rounded-lg"
        :class="{
          'bg-blue-500 text-white': link.active,
          'bg-gray-200 text-gray-700 hover:bg-gray-300': !link.active,
          'cursor-not-allowed opacity-50': !link.url
        }"
        v-html="link.label"
      />
      <span
        v-else
        class="px-4 py-2 border rounded-lg bg-gray-200 text-gray-700 cursor-not-allowed"
        v-html="link.label"
      />
    </template>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Link,
  },
  props: {
    links: {
      type: Array,
      required: true,
    },
  },
};
</script>