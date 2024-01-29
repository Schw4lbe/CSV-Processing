<template>
  <div>
    <area-chart
      :data="{ '2017-01-01 00:00:00 -0800': 2, '2017-01-01 00:01:00 -0800': 5 }"
    ></area-chart>
  </div>
</template>

<script>
export default {
  name: "LineChart",
};
</script>

<style></style>
