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
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">User</label>
                                            <select v-model="form.user_id" class="form-select">
                                                <option disabled value="">Select a User</option>
                                                <option v-for="user in users" :key="user.id" :value="user.id">
                                                    {{ user.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Month</label>
                                            <input type="text" v-model="form.month" class="form-control"
                                                placeholder="e.g. April 2025" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Flat Rent</label>
                                            <input type="number" v-model="form.flat_rent" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Gas Bill</label>
                                            <input type="number" v-model="form.gas_bill" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Electric Bill</label>
                                            <input type="number" v-model="form.electric_bill" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Water Bill</label>
                                            <input type="number" v-model="form.water_bill" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Garbage Bill</label>
                                            <input type="number" v-model="form.garbase_bill" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Due</label>
                                            <input type="number" v-model="form.due" class="form-control" />
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Payment</label>
                                            <input type="number" v-model="form.payment" class="form-control" />
                                        </div>
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
                                <span>হিসাবের তালিকা</span>
                                <input type="text" v-model="searchQuery" class="form-control w-25 "
                                    placeholder="Search by name, phone, or address" />
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered m-2">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Month</th>
                                        <th>Flat Rent</th>
                                        <th>Gas Bill</th>
                                        <th>Electric Bill</th>
                                        <th>Water Bill</th>
                                        <th>Grand Total</th>
                                        <th>Due</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="rentBill in filteredrentBills" :key="rentBill.id">
                                        <td>{{ rentBill.users?.name || 'N/A' }}</td>
                                        <td>{{ rentBill.month }}</td>
                                        <td>{{ rentBill.flat_rent }}</td>
                                        <td>{{ rentBill.gas_bill }}</td>
                                        <td>{{ rentBill.electric_bill }}</td>
                                        <td>{{ rentBill.water_bill }}</td>
                                        <td>{{ rentBill.grand_total }}</td>
                                        <td>{{ rentBill.due }}</td>
                                        <td>{{ rentBill.payment }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm m-1" @click="editrentBill(rentBill)">
                                                Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm m-1"
                                                @click="deleterentBill(rentBill.id)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <nav v-if="pagination" aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                                        <button class="page-link" @click="fetchrentBills(pagination.current_page - 1)"
                                            :disabled="!pagination.prev_page_url">
                                            Previous
                                        </button>
                                    </li>
                                    <li class="page-item" v-for="page in totalPages" :key="page"
                                        :class="{ active: page === pagination.current_page }">
                                        <button class="page-link" @click="fetchrentBills(page)">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                                        <button class="page-link" @click="fetchrentBills(pagination.current_page + 1)"
                                            :disabled="!pagination.next_page_url">
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
                user_id: '',
                month: '',
                flat_rent: '',
                gas_bill: '',
                electric_bill: '',
                water_bill: '',
            },
            rentBills: [],
            pagination: null,
            searchQuery: '',
            isEditing: false,
            currentrentBillId: null,
            showForm: false,
            showList: true,
            errors: [],
        };
    },
    mounted() {
        this.fetchrentBills();
        this.fetchUsers();
    },
    computed: {
        filteredrentBills() {
            if (!this.searchQuery) return this.rentBills;
            const query = this.searchQuery.toLowerCase();
            return this.rentBills.filter(rentBill =>
                rentBill.users.name.toLowerCase().includes(query)
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
        async fetchrentBills(page = 1) {
            try {
                const response = await axios.get('/api/rentBills', {
                    params: { page, search: this.searchQuery }
                });
                this.rentBills = response.data.data;
                this.pagination = response.data;
            } catch (error) {
                console.error('Error fetching rentBills:', error);
            }
        },
        async fetchUsers() {
            try {
                const response = await axios.get('/api/employees');
                this.users = response.data.data; // Adjust if your data structure is different
            } catch (error) {
                console.error('Error fetching users:', error);
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
                    // Update rentBill
                    await axios.post(`/api/rentBills/${this.currentrentBillId}`, formData);
                    alert('rentBill updated successfully!');
                } else {
                    // Create new rentBill
                    await axios.post('/api/rentBills', formData);
                    alert('rentBill added successfully!');
                }
                this.fetchrentBills(this.pagination.current_page);
                this.toggleForm();
            } catch (error) {
                console.error('Error saving rentBill:', error);

                if (error.response) {
                    console.error('Response error data:', error.response.data);
                    this.errors = error.response.data.errors || ['Failed to save rentBill.'];
                }
            }
        },
        async deleterentBill(id) {
            if (!confirm('Are you sure you want to delete this rentBill?')) return;
            try {
                await axios.delete(`/api/rentBills/${id}`);
                alert('rentBill deleted successfully!');
                this.fetchrentBills(this.pagination.current_page);
            } catch (error) {
                console.error('Error deleting rentBill:', error);
            }
        },
        editrentBill(rentBill) {
            this.isEditing = true;
            this.currentrentBillId = rentBill.id;
            this.form = {
                user_id: rentBill.user_id,
                month: rentBill.month,
                flat_rent: rentBill.flat_rent,
                gas_bill: rentBill.gas_bill,
                electric_bill: rentBill.electric_bill,
                garbase_bill: rentBill.garbase_bill,
                payment: rentBill.payment,
                due: rentBill.due,
                water_bill: rentBill.water_bill
            };
            if (!this.showForm) this.toggleForm();
        },
        resetForm() {
            this.form = { user_id: '', month: '', flat_rent: '', gas_bill: '', electric_bill: '', water_bill: '', };
            this.isEditing = false;
            this.currentrentBillId = null;
            this.errors = [];
        }
    }
};
</script>
