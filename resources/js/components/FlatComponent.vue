<template>
    <div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="row">
                    <div class="col-md-2">
                        <button class="btn btn-primary mb-3" @click="toggleForm">
                            {{ showForm ? 'Back' : 'Add New' }}
                        </button>
                    </div>
                </div>
                <div v-if="showForm" class="col-md-4">
                    <div class="card m-2">
                        <div class="card-header">
                            {{ isEditing ? 'Edit' : 'Add ' }}
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <div class="form-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" v-model="form.name" class="form-control" />

                                    <div v-if="errors.length" class="alert alert-danger mt-2">
                                        <ul>
                                            <li v-for="error in errors" :key="error">{{ error }}</li>
                                        </ul>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-3">
                                        {{ isEditing ? 'Update' : 'Submit' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div v-if="showList" class="col-md-4">
                    <div class="card m-2">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>ফ্লাট তালিকা</span>
                                    <input type="text" v-model="searchQuery" class="form-control w-50" placeholder="Search by name" />
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered m-2">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="flat in filteredflats" :key="flat.id">
                                        <td>{{ flat.name }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm m-1" @click="editflat(flat)">
                                                Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm m-1" @click="deleteflat(flat.id)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <nav v-if="pagination" aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                                        <button class="page-link" @click="fetchflats(pagination.current_page - 1)" :disabled="!pagination.prev_page_url">
                                            Previous
                                        </button>
                                    </li>
                                    <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === pagination.current_page }">
                                        <button class="page-link" @click="fetchflats(page)">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                                        <button class="page-link" @click="fetchflats(pagination.current_page + 1)" :disabled="!pagination.next_page_url">
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                name: '',
            },
            flats: [],
            pagination: null,
            searchQuery: '',
            isEditing: false,
            currentflatId: null,
            showForm: false,
            showList: true,
            errors: [],
        };
    },
    mounted() {
        this.fetchflats();
    },
    computed: {
        filteredflats() {
            if (!this.searchQuery) return this.flats;
            const query = this.searchQuery.toLowerCase();
            return this.flats.filter(flat =>
                flat.name.toLowerCase().includes(query)
            );
        },
        totalPages() {
            return this.pagination ? Array.from({ length: this.pagination.last_page }, (_, i) => i + 1) : [];
        }
    },
    methods: {
        toggleForm() {
            this.showForm = !this.showForm;
            this.showList = !this.showForm;
            if (!this.showForm) this.resetForm();
        },
        handleImageUpload(event) {
            this.form.image = event.target.files[0];
        },
        async fetchflats(page = 1) {
            try {
                const response = await axios.get('/api/flats', {
                    params: { page, search: this.searchQuery }
                });
                this.flats = response.data.data;
                this.pagination = response.data;
            } catch (error) {
                console.error('Error fetching flats:', error);
            }
        },
        async submitForm() {
            const formData = new FormData();

            // Append fields to FormData
            for (const key in this.form) {
                if (this.form[key] !== null && this.form[key] !== '') {
                    if (key === 'image' && this.form.image) {
                        // If a new image is selected, append it
                        formData.append('image', this.form.image);
                    } else if (key === 'image' && !this.form.image) {
                        // If no new image, append the existing image URL
                        if (this.form.image_url) {
                            formData.append('image_url', this.form.image_url);
                        }
                    } else {
                        formData.append(key, this.form[key]);
                    }
                }
            }

            console.log("Form Data to send:", formData);  // Log form data for debugging

            try {
                if (this.isEditing) {
                    // Update flat
                    await axios.post(`/api/flats/${this.currentflatId}`, formData);
                    alert('flat updated successfully!');
                } else {
                    // Create new flat
                    await axios.post('/api/flats', formData);
                    alert('flat added successfully!');
                }
                this.fetchflats(this.pagination.current_page);
                this.toggleForm();
            } catch (error) {
                console.error('Error saving flat:', error);

                if (error.response) {
                    console.error('Response error data:', error.response.data);
                    this.errors = error.response.data.errors || ['Failed to save flat.'];
                }
            }
        },
        async deleteflat(id) {
            if (!confirm('Are you sure you want to delete this flat?')) return;
            try {
                await axios.delete(`/api/flats/${id}`);
                alert('flat deleted successfully!');
                this.fetchflats(this.pagination.current_page);
            } catch (error) {
                console.error('Error deleting flat:', error);
            }
        },
        editflat(flat) {
            this.isEditing = true;
            this.currentflatId = flat.id;
            this.form = { 
                name: flat.name, 
            };
            if (!this.showForm) this.toggleForm();
        },
        resetForm() {
            this.form = { name: '' };
            this.isEditing = false;
            this.currentflatId = null;
            this.errors = [];
        }
    }
};
</script>
