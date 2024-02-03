<template>
  <v-select
    v-model="searchCategory"
    :items="searchCategories"
    label="Suchkategorie"
    dense
    hide-details
    outlined
    small
    color="primary"
  ></v-select>
  <v-text-field
    v-model="searchQuery"
    append-icon="mdi-magnify"
    label="Suchen"
    single-line
    hide-details
    color="primary"
    :disabled="!searchCategory"
  ></v-text-field>
</template>
<script>
export default {
  name: "SearchBar",
  props: {
    searchCategories: Array,
  },

  data() {
    return {
      searchCategory: "",
      searchQuery: "",
    };
  },
};
</script>
