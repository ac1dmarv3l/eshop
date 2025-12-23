'use strict';

export default function (Alpine) {
    document.addEventListener('alpine:init', () => {
        Alpine.data('form', () => ({
            open: false,
            loading: false,
            message: '',
            showForm() {
                this.open = true;
            },
            async submitForm(event) {
                event.preventDefault();
                this.loading = true;
                this.message = '';

                const formData = new FormData(event.target);
                try {
                    const response = await fetch('http://localhost:8010/AddToCartController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    this.message = result.message;
                    if (result.success) {
                        event.target.reset();
                    }
                } catch (error) {
                    this.message = 'Error submitting form';
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
}
