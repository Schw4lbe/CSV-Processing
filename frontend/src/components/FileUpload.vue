<template>
  <div v-if="!getTableName" class="form-wrapper">
    <img :src="background" alt="Hintergrundbild" />
    <div class="form-container">
      <form @submit.prevent="onSubmit">
        <label for="csv">CSV-Datei hochladen</label>
        <input id="csv" ref="fileInput" type="file" @change="onFileChange" />
        <p v-if="isCsv === null" class="msg-csv-pending">
          CSV Datei auswählen...
        </p>
        <p v-if="isCsv === true" class="msg-csv-valid">
          CSV ausgewählt<i class="fa-solid fa-circle-check"></i>
        </p>
        <button type="submit">importieren</button>
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from "vuex";
import img from "../../public/assets/img/background.jpg";

export default {
  name: "FileUpload",

  data() {
    return {
      selectedFile: null,
      isCsv: null,
      background: img,
    };
  },

  computed: {
    ...mapGetters(["getTableName"]),
  },

  methods: {
    ...mapActions(["uploadCsv"]),
    ...mapMutations([
      "setErrorCode",
      "setSuccessCode",
      "triggerLoadingAnimation",
      "unsetLoadingAnimation",
    ]),

    onFileChange(e) {
      const file = e.target.files[0];
      if (file && this.isValidFile(file)) {
        this.selectedFile = file;
        this.isCsv = true;
      } else if (!this.isValidFile(file)) {
        this.$refs.fileInput.value = "";
      }
    },

    isValidFile(file) {
      const maxSize = 5 * 1024 * 1024;
      const invalidChars = /[\]/*?"<>|\\]/;
      const parentDirectoryTraversal = /\.\./;
      const validTypes = ["text/csv", "application/vnd.ms-excel"];

      if (file.size > maxSize) {
        this.setErrorCode("FEE01");
        return false;
      }

      if (
        invalidChars.test(file.name) ||
        parentDirectoryTraversal.test(file.name)
      ) {
        this.setErrorCode("FEE02");
        return false;
      }

      if (!validTypes.includes(file.type)) {
        this.setErrorCode("FEE03");
        return false;
      }

      return true;
    },

    async onSubmit() {
      if (!this.selectedFile) {
        this.setErrorCode("FEE04");
      } else if (this.selectedFile) {
        this.triggerLoadingAnimation();

        const formData = new FormData();
        formData.append("file", this.selectedFile);

        // Reset UI
        this.$refs.fileInput.value = "";
        this.selectedFile = null;
        this.isCsv = null;
        try {
          const response = await this.uploadCsv(formData);
          if (response && response.success) {
            this.setSuccessCode("FES01");
          } else {
            this.setErrorCode("FEE05");
            return { success: false };
          }
        } catch (error) {
          console.error("Error in onSubmit method:", error);
          throw error;
        } finally {
          this.unsetLoadingAnimation();
        }
      }
    },
  },
};
</script>
