export default {
  getUploadSuccessMsg: (state) => state.uploadSuccessMsg,
  getTableName: (state) => state.tableName,
  getChartData: (state) => state.chartData,

  // getters properties to handle errors, warnings, success msg
  getErrorCode: (state) => state.errorCode,
  getWarningCode: (state) => state.warningCode,
  getSuccessCode: (state) => state.successCode,

  // laoding animation
  getLoadingState: (state) => state.isLoading,
};
