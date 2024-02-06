export default {
  // getUploadSuccessMsg throws error upon deletion; needs to be adressed somewhat else
  getUploadSuccessMsg: () => "Default message or null",

  getTableName: (state) => state.tableName,
  getChartData: (state) => state.chartData,

  // getters properties to handle errors, warnings, success msg
  getErrorCode: (state) => state.errorCode,
  getWarningCode: (state) => state.warningCode,
  getSuccessCode: (state) => state.successCode,

  // laoding animation
  getLoadingState: (state) => state.isLoading,
};
