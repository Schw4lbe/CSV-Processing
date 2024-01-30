export default {
  setUploadSuccessMsg(state, message) {
    state.uploadSuccessMsg = message;
    localStorage.setItem("uploadSuccessMsg", message);
  },

  setTableName(state, name) {
    state.tableName = name;
    localStorage.setItem("tableName", name);
  },

  setTableData(state, data) {
    state.tableData = data;
    console.log(state);
  },
};
