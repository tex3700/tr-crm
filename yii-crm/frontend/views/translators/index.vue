
<template>
  <div>
    <h2>{{ title }}</h2>
    <ul v-if="translators.length">
      <li v-for="translator in translators" :key="translator.id">
        {{ translator.name }} - {{ translator.email }}
      </li>
    </ul>
    <p v-else>Нет доступных переводчиков</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      translators: [],
      title: ''
    };
  },
  mounted() {
    this.loadTranslators();
    this.setTitle();
  },
  methods: {
    loadTranslators() {
      fetch('/api/translators')
          .then(response => response.json())
          .then(data => this.translators = data);
    },
    setTitle() {
      const isWeekend = new Date().getDay() === 0 || new Date().getDay() === 6;
      this.title = isWeekend
          ? 'Переводчики доступные в выходные'
          : 'Переводчики доступные в будни';
    }
  }
};
</script>