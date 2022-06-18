async function requestForm() {
  return Swal.fire({
    title: 'Locação de veículo',
    html: `
    <form>
      <div class="form-group">
        <label for="name">Nome:</label>
        <input id="name" class="swal2-input" placeholder="Fulano..." required>
      </div>
      <div class="form-group">
        <label for="finished_at">Devolução em:</label>
        <input type="date" id="finished_at" class="swal2-input" required>
      </div>
    </form>
    `,
    focusConfirm: false,
    confirmButtonText: 'ALUGAR',
    cancelButtonText: 'VOLTAR',
    showCancelButton: true,
    showLoaderOnConfirm: true,
    preConfirm: () => ({
      name: document.getElementById('name').value,
      finished_at: document.getElementById('finished_at').value,
    }),
  });
}

document.addEventListener('alpine:init', () => {
  Alpine.store('cars', {
    data: [],
    isLoading: false,
    async init() {
      try {
        const { data } = await axios.get('/cars');
        this.data = data;
      } catch (error) {
        Swal.fire(error.response.message);
      }
    },
    async rent(id_car) {
      try {
        const result = await requestForm();

        if (!result.isConfirmed) return;

        if (!result.value.name) {
          return Swal.fire({ icon: 'error', title: 'Nome é obrigatório.' });
        }

        if (!result.value.finished_at) {
          return Swal.fire({
            icon: 'error',
            title: 'Data de devolução é obrigatório.',
          });
        }
        this.isLoading = true;
        await axios.post('/rent', {
          name: result.value.name,
          finished_at: result.value.finished_at,
          started_at: new Date().toISOString(),
          id_car,
        });
        this.isLoading = false;
        this.init();
        Swal.fire({ icon: 'success', title: 'Carro alocado com sucesso.' });
      } catch (error) {
        this.init();
        this.isLoading = false;
        Swal.fire({ icon: 'error', title: error.response.data.message });
      }
    },
    async unrent(id_car) {
      try {
        this.isLoading = true;
        await axios.put('/unrent', { id_car });
        this.init();
        this.isLoading = false;
        Swal.fire({ icon: 'success', title: 'Carro devolvido com sucesso.' });
      } catch (error) {
        this.isLoading = false;
        this.init();
        Swal.fire({ icon: 'error', title: error.response.data.message });
      }
    },
    handle(id_car, status) {
      if (status === 'available') {
        this.rent(id_car);
      }
      if (status === 'rented') {
        this.unrent(id_car);
      }
    },
  });
});
