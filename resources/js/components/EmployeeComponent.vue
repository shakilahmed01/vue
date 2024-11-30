<template>
    <div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <!-- Add Employee Button -->
                    <button class="btn btn-primary mb-3" @click="toggleForm">
                        {{ showForm ? 'Close Form' : 'Add Employee' }}
                    </button>

                    <!-- Add Employee Form -->
                    <div v-if="showForm" class="card m-2">
                        <div class="card-header">
                            {{ isEditing ? 'Edit Employee' : 'Add Employee' }}
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <div class="form-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" v-model="form.name" class="form-control" />

                                    <label class="form-label">Phone</label>
                                    <input type="text" v-model="form.phone" class="form-control" />

                                    <label class="form-label">Address</label>
                                    <input type="text" v-model="form.address" class="form-control" />

                                    <button type="submit" class="btn btn-primary mt-3">
                                        {{ isEditing ? 'Update' : 'Submit' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Search and Employee List -->
                    <div class="card m-2">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>List Employees</span>
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    class="form-control w-50"
                                    placeholder="Search by name, phone, or address"
                                />
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered m-2">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="employee in filteredEmployees" :key="employee.id">
                                        <td>{{ employee.name }}</td>
                                        <td>{{ employee.phone }}</td>
                                        <td>{{ employee.address }}</td>
                                        <td>
                                            <button
                                                class="btn btn-warning btn-sm m-1"
                                                @click="editEmployee(employee)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="btn btn-danger btn-sm m-1"
                                                @click="deleteEmployee(employee.id)"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination Controls -->
                            <nav v-if="pagination" aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                                        <button
                                            class="page-link"
                                            @click="fetchEmployees(pagination.current_page - 1)"
                                            :disabled="!pagination.prev_page_url"
                                        >
                                            Previous
                                        </button>
                                    </li>
                                    <li
                                        class="page-item"
                                        v-for="page in totalPages"
                                        :key="page"
                                        :class="{ active: page === pagination.current_page }"
                                    >
                                        <button class="page-link" @click="fetchEmployees(page)">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                                        <button
                                            class="page-link"
                                            @click="fetchEmployees(pagination.current_page + 1)"
                                            :disabled="!pagination.next_page_url"
                                        >
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
                address: ''
            },
            employees: [],
            pagination: null, // Pagination data from the API
            searchQuery: '',
            isEditing: false,
            currentEmployeeId: null,
            showForm: false // Form visibility state
        };
    },
    mounted() {
        this.fetchEmployees(); // Fetch the first page of employees on mount
    },
    computed: {
        filteredEmployees() {
            if (!this.searchQuery) {
                return this.employees;
            }
            const query = this.searchQuery.toLowerCase();
            return this.employees.filter(employee =>
                employee.name.toLowerCase().includes(query) ||
                employee.phone.toLowerCase().includes(query) ||
                employee.address.toLowerCase().includes(query)
            );
        },
        totalPages() {
            return this.pagination
                ? Array.from({ length: this.pagination.last_page }, (_, i) => i + 1)
                : [];
        }
    },
    methods: {
        toggleForm() {
            this.showForm = !this.showForm;
            if (!this.showForm) {
                this.resetForm(); // Reset form when closing
            }
        },
        async fetchEmployees(page = 1) {
            try {
                const response = await axios.get(`/api/employees?page=${page}`);
                this.employees = response.data.data;
                this.pagination = response.data; // Store pagination data
            } catch (error) {
                console.error('Error fetching employees:', error);
            }
        },
        async submitForm() {
            if (this.isEditing) {
                await this.updateEmployee();
            } else {
                await this.addEmployee();
            }
        },
        async addEmployee() {
            try {
                await axios.post('/api/employees', this.form);
                alert('Employee added successfully!');
                this.fetchEmployees(this.pagination.current_page); // Refresh current page
                this.toggleForm(); // Close the form
            } catch (error) {
                console.error('Error adding employee:', error);
            }
        },
        async updateEmployee() {
            try {
                await axios.put(`/api/employees/${this.currentEmployeeId}`, this.form);
                alert('Employee updated successfully!');
                this.fetchEmployees(this.pagination.current_page); // Refresh current page
                this.toggleForm(); // Close the form
            } catch (error) {
                console.error('Error updating employee:', error);
            }
        },
        editEmployee(employee) {
            this.isEditing = true;
            this.currentEmployeeId = employee.id;
            this.form = { ...employee }; // Populate the form
            if (!this.showForm) {
                this.toggleForm(); // Show the form if it's not already visible
            }
        },
        async deleteEmployee(id) {
            if (!confirm('Are you sure you want to delete this employee?')) return;
            try {
                await axios.delete(`/api/employees/${id}`);
                alert('Employee deleted successfully!');
                this.fetchEmployees(this.pagination.current_page); // Refresh current page
            } catch (error) {
                console.error('Error deleting employee:', error);
            }
        },
        resetForm() {
            this.form = { name: '', phone: '', address: '' };
            this.isEditing = false;
            this.currentEmployeeId = null;
        }
    }
};
</script>


