<template>
  <div>
    <form>
      <h3>csv upload</h3>
      <label for="csv">CSV-Datei hochladen</label>
      <input id="csv" type="file" />
      <button>Upload</button>
    </form>
  </div>
</template>

<script>
export default {
  name: "FileUpload",
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
