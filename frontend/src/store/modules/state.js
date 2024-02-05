export default {
  uploadSuccessMsg: localStorage.getItem("uploadSuccessMsg") || null,
  tableName: localStorage.getItem("tableName") || null,
  chartData: localStorage.getItem("chartData") || null,

  // states properties to handle errors, warnings, success msg
  errorCode: null,
  warningCode: null,
  successCode: null,
};
