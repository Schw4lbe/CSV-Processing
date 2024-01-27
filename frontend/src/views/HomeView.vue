<template>
  <div class="home">
    <FileUpload />
  </div>
</template>

<script>
import FileUpload from "@/components/FileUpload.vue";

export default {
  name: "HomeView",
  components: {
    FileUpload,
  },
};
</script>
