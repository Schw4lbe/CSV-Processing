<template>
  <div>
    <form @submit.prevent="onSubmit">
      <h3>csv upload</h3>
      <label for="csv">CSV-Datei hochladen</label>
      <input id="csv" type="file" @change="onFileChange" ref="fileInput" />
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
      const file = e.target.files[0];
      // first layer of security check file on component frontend level
      if (file && this.isValidFile(file)) {
        this.selectedFile = file;
      } else if (!this.isValidFile(file)) {
        this.$refs.fileInput.value = ""; // Reset file input in UI
      }
    },

    isValidFile(file) {
      const maxSize = 5 * 1024 * 1024; // 5MB of size
      const invalidChars = /[\]/*?"<>|\\]/; // Regex for special characters
      const parentDirectoryTraversal = /\.\./; // Regex to prefent directory traversal attacks
      const validTypes = ["text/csv", "application/vnd.ms-excel"]; // MIME types basic check

      if (file.size > maxSize) {
        alert("File to large. Max size 5MB.");
        return false;
      }

      if (
        invalidChars.test(file.name) ||
        parentDirectoryTraversal.test(file.name)
      ) {
        alert(
          "Filename contains invalid characters. Please Rename your csv and try again."
        );
        return false;
      }

      if (!validTypes.includes(file.type)) {
        alert("Invalid file type. Please select CSV file.");
        return false;
      }

      return true;
    },

    onSubmit() {
      if (this.selectedFile) {
        // use build in web API FormData to set key/value pairs
        const formData = new FormData();
        // adds file to formData Object for backend
        formData.append("file", this.selectedFile);
        // dispatch action with formData payload
        this.uploadCsv(formData);
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
