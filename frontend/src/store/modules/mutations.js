export default {
  setUploadSuccessMsg(state, message) {
    state.uploadSuccessMsg = message;
    localStorage.setItem("uploadSuccessMsg", message);
  },

  setTableName(state, name) {
    state.tableName = name;
    localStorage.setItem("tableName", name);
  },

  storeChartData(state, data) {
    state.chartData = data;
    localStorage.setItem("chartData", JSON.stringify(data));
  },

  unsetSessionData(state) {
    state.chartData = null;
    state.tableName = null;
    state.uploadSuccessMsg = null;
    localStorage.removeItem("chartData");
    localStorage.removeItem("tableName");
    localStorage.removeItem("uploadSuccessMsg");
  },
};
