<template>
  <div>
    <form @submit.prevent="onSubmit">
      <h3>csv upload</h3>
      <label for="csv">CSV-Datei hochladen</label>
      <input id="csv" type="file" @change="onFileChange" />
      <button type="submit">Upload</button>
    </form>
  </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "FileUpload",

  data() {
    return {
      selectedFile: null,
    };
  },

  methods: {
    ...mapActions(["uploadCsv"]),

    // cache file on change for later use
    onFileChange(e) {
      this.selectedFile = e.target.files[0];
    },

    onSubmit() {
      if (this.selectedFile) {
        // use build in web API FormData to set key/value pairs
        const uploadData = new FormData();
        // adds file to formData Object for backend
        formData.append("file", this.selectedFile);
        // dispatch action with formData payload
        this.uploadCsv(uploadData);
      } else {
        alert("select file to upload.");
        return;
      }
    },
  },
};
</script>

<style scoped>
form {
  display: flex;
  flex-direction: column;
  align-items: center;
  border-bottom: 1px solid black;
}

label {
  padding: 0.5rem;
}

button {
  padding: 0.5rem 1rem;
  margin: 1rem;
}
</style>
