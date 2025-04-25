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
                <div class="col-md-4">
                    <div v-if="showForm" class="card m-2">
                        <div class="card-header">
                            {{ isEditing ? 'Edit' : 'Add ' }}
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <div class="form-group">
                                    <label class="form-label">Flat</label>
                                    <select v-model="form.flat_id" class="form-control">
                                        <option disabled value="">Select a Flat</option>
                                        <option v-for="flat in flats" :key="flat.id" :value="flat.id">
                                            {{ flat.name }}
                                        </option>
                                    </select>
                                    <label class="form-label">Name</label>
                                    <input type="text" v-model="form.name" class="form-control" />

                                    <label class="form-label">Phone</label>
                                    <input type="text" v-model="form.phone" class="form-control" />

                                    <label class="form-label">Address</label>
                                    <input type="text" v-model="form.address" class="form-control" />

                                    <label class="form-label">Image</label>
                                    <input type="file" ref="image" class="form-control" @change="handleImageUpload" />
                                    <!-- Display current image if editing -->
                                    <div v-if="isEditing && form.image_url">
                                        <img :src="form.image_url" alt="Employee Image" class="img-thumbnail" width="100" />
                                    </div>

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

                <div v-if="showList" class="col-md-12">
                    <div class="card m-2">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>ভাড়াটিয়াদের তালিকা</span>
                                    <input type="text" v-model="searchQuery" class="form-control w-25 " placeholder="Search by name, phone, or address" />
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered m-2">
                                <thead>
                                    <tr>
                                        <th>Flat</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="employee in filteredEmployees" :key="employee.id">
                                        <td>{{ employee.flats?.name || 'N/A' }}</td>
                                        <td>{{ employee.name }}</td>
                                        <td>{{ employee.phone }}</td>
                                        <td>{{ employee.address }}</td>
                                        <td>
                                            <img :src="employee.image_url" alt="Employee Image" class="img-thumbnail" width="100" />
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm m-1" @click="editEmployee(employee)">
                                                Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm m-1" @click="deleteEmployee(employee.id)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <nav v-if="pagination" aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                                        <button class="page-link" @click="fetchEmployees(pagination.current_page - 1)" :disabled="!pagination.prev_page_url">
                                            Previous
                                        </button>
                                    </li>
                                    <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === pagination.current_page }">
                                        <button class="page-link" @click="fetchEmployees(page)">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                                        <button class="page-link" @click="fetchEmployees(pagination.current_page + 1)" :disabled="!pagination.next_page_url">
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
                phone: '',
                address: '',
                image: null,
                image_url: '', // Store the image URL when editing
                flat_id: '',
            },
            employees: [],
            pagination: null,
            searchQuery: '',
            isEditing: false,
            currentEmployeeId: null,
            showForm: false,
            showList: true,
            errors: [],
        };
    },
    mounted() {
        this.fetchEmployees();
        this.fetchFlats();
    },
    computed: {
        filteredEmployees() {
            if (!this.searchQuery) return this.employees;
            const query = this.searchQuery.toLowerCase();
            return this.employees.filter(employee =>
                employee.name.toLowerCase().includes(query) ||
                employee.phone.toLowerCase().includes(query) ||
                employee.address.toLowerCase().includes(query)
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
        async fetchEmployees(page = 1) {
            try {
                const response = await axios.get('/api/employees', {
                    params: { page, search: this.searchQuery }
                });
                this.employees = response.data.data;
                this.pagination = response.data;
            } catch (error) {
                console.error('Error fetching employees:', error);
            }
        },
        async fetchFlats() {
            try {
                const response = await axios.get('/api/flats');
                this.flats = response.data.data; // Adjust if your data structure is different
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
                    // Update employee
                    await axios.post(`/api/employees/${this.currentEmployeeId}`, formData);
                    alert('Employee updated successfully!');
                } else {
                    // Create new employee
                    await axios.post('/api/employees', formData);
                    alert('Employee added successfully!');
                }
                this.fetchEmployees(this.pagination.current_page);
                this.toggleForm();
            } catch (error) {
                console.error('Error saving employee:', error);

                if (error.response) {
                    console.error('Response error data:', error.response.data);
                    this.errors = error.response.data.errors || ['Failed to save employee.'];
                }
            }
        },
        async deleteEmployee(id) {
            if (!confirm('Are you sure you want to delete this employee?')) return;
            try {
                await axios.delete(`/api/employees/${id}`);
                alert('Employee deleted successfully!');
                this.fetchEmployees(this.pagination.current_page);
            } catch (error) {
                console.error('Error deleting employee:', error);
            }
        },
        editEmployee(employee) {
            this.isEditing = true;
            this.currentEmployeeId = employee.id;
            this.form = { 
                name: employee.name, 
                phone: employee.phone, 
                address: employee.address, 
                image_url: employee.image_url, // Ensure image URL is set for editing
                image: null, // Reset the image field for editing
                flat_id: employee.flat_id
            };
            if (!this.showForm) this.toggleForm();
        },
        resetForm() {
            this.form = { name: '', phone: '', address: '', image: null, image_url: '' };
            this.isEditing = false;
            this.currentEmployeeId = null;
            this.errors = [];
        }
    }
};
</script>
