document.addEventListener('alpine:init', () => {
  Alpine.store('cars', {
    data: [
      {
        model: 'helo',
      },
    ],
    async init() {
      try {
        const response = await fetch('/cars');
        this.data = await response.json();
      } catch (error) {
        swal(error.response.message);
      }
    },
    async rent(id) {
      try {
        await fetch({
          url: '/rent',
          body: JSON.stringify({}),
        });
      } catch (error) {
        swal(error.response.message);
      }
    },
  });
});
