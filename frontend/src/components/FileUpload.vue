<template>
  <div v-if="!getTableName" class="form-wrapper">
    <img :src="background" alt="Hintergrundbild" />
    <div class="form-container">
      <form @submit.prevent="onSubmit">
        <!-- move to modal -->
        <p v-if="getUploadSuccessMsg" class="upload-success-msg">
          {{ getUploadSuccessMsg }}
        </p>
        <!-- ############## -->

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
    ...mapGetters(["getUploadSuccessMsg", "getTableName"]),
  },

  methods: {
    ...mapActions(["uploadCsv"]),
    ...mapMutations([
      "setErrorCode",
      "setSuccessCode",
      "triggerLoadingAnimation",
      "unsetLoadingAnimation",
    ]),

    // cache file on change for later use
    onFileChange(e) {
      const file = e.target.files[0];
      // first layer of security check file on component frontend level
      if (file && this.isValidFile(file)) {
        this.selectedFile = file;
        this.isCsv = true;
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
        //start loading animation
        this.triggerLoadingAnimation();
        // use build in web API FormData to set key/value pairs
        const formData = new FormData();
        // adds file to formData Object for backend
        formData.append("file", this.selectedFile);
        this.$refs.fileInput.value = ""; // Reset file input in UI
        this.selectedFile = null; // Reset selected file in cache
        this.isCsv = null;
        try {
          // dispatch action with formData payload
          const response = await this.uploadCsv(formData);
          if (response && response.success) {
            this.setSuccessCode("FES01");
            this.unsetLoadingAnimation();
          } else {
            this.unsetLoadingAnimation();
            return { success: false };
          }
        } catch (error) {
          console.error("Error in onSubmit method:", error);
          throw error;
        }
      }
    },
  },
};
</script>

<style scoped>
.form-wrapper {
  height: 100vh;
  width: 100vw;
  /* background: rgba(255, 255, 255, 0.7); */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 3;
}

img {
  height: 100%;
  width: 100%;
  position: absolute;
  object-fit: cover;
  filter: blur(5px) brightness(40%) sepia(100%) saturate(80%);
  z-index: 1;
}

.form-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 350px;
  width: 450px;
  background: rgba(255, 255, 255, 1);
  border-radius: 5px;
  box-shadow: 15px 15px 10px #333;
  z-index: 2;
}

form {
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

label,
input {
  text-align: center;
  width: 80%;
  color: #222;
  border-bottom: 1px solid #222;
}

#csv {
  text-align: left;
  padding: 1rem;
}

label {
  padding: 0.5rem;
  font-size: 1.4rem;
}

.msg-csv-pending {
  color: #aaa;
  font-style: italic;
  padding: 0.5rem;
  margin-top: 10px;
}

.msg-csv-valid {
  color: rgb(26, 170, 26);
  padding: 0.5rem;
  margin-top: 10px;
}

.msg-csv-invalid {
  color: tomato;
  padding: 0.5rem;
  margin-top: 10px;
}

i {
  padding: 0.5rem;
}

button {
  padding: 0.5rem 1rem;
  margin: 1rem;
  background: #444;
  text-transform: uppercase;
  width: 200px;
  transition: all 0.3s;
}

button:hover {
  background: #222;
  color: #2194f0;
}
</style>
