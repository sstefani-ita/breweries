<script setup>
    import { ref, onMounted } from 'vue';
    import { TailwindPagination } from 'laravel-vue-pagination';

    const username = ref('');
    const password = ref('');
    const loginMessage = ref('');
    const isAuthenticated = ref(false);
    const breweries = ref({});
    const per_page = ref(10)

    const login = async () => {
        try {
            const response = await axios.post('/api/login', {
                username: username.value,
                password: password.value
            });

            if(response.data.data.token) {

                // Better -> https://laravel.com/docs/11.x/sanctum#spa-authentication
                localStorage.setItem('token', response.data.data.token);

                isAuthenticated.value = true;
                loginMessage.value = '';

                getResults(1);
            }
        } catch (error) {
            loginMessage.value = 'Login failed.';
        }
    };

    const getResults = async (page, per_page) => {
        try {
            const response = await axios.get('/api/v1/data', {
                params: {
                    page: page,
                    per_page: per_page
                },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`,
                }
            });

            if (response.data) {
                breweries.value = response.data;
            }
        } catch (error) {
            console.error('Error fetching breweries:', error);
        }
    };

    onMounted(async () => {
        if(localStorage.getItem('token')) {
            isAuthenticated.value = true;
            getResults(1);
        }
    });
</script>

<template>
    <div class="container mx-auto">

        <div v-if="!isAuthenticated">
            <h1 class="text-3xl font-bold underline mb-4">Login</h1>

            <form @submit.prevent="login" class="space-y-4">
                <p v-if="loginMessage" class="bg-red-500 text-white mb-2">{{ loginMessage }}</p>
                <div>
                    <label class="block font-bold">Username:</label>
                    <input v-model="username" type="text" class="border rounded p-2 w-full" placeholder="Username (root)" required />
                </div>
                <div>
                    <label class="block font-bold">Password:</label>
                    <input v-model="password" type="password" class="border rounded p-2 w-full" placeholder="Passoword (password)" required />
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Login
                </button>
            </form>
        </div>

        <div v-else>
            <h1 class="text-3xl font-bold underline mb-4">Breweries</h1>

            <table class="border border-gray-300 mb-4" v-if="breweries.data">
                <thead>
                    <tr>
                        <td class="border border-gray-300 p-3 font-bold">ID</td>
                        <td class="border border-gray-300 p-3 font-bold">Name</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="brewery in breweries.data" :key="brewery.id">
                        <td class="border border-gray-300 p-3">{{ brewery.id }}</td>
                        <td class="border border-gray-300 p-3">{{ brewery.name }}</td>
                    </tr>
                </tbody>
            </table>

            <p v-else>No breweries found.</p>

            <TailwindPagination
                :data="breweries"
                :limit="2"
                @pagination-change-page="(page) => getResults(page, per_page)"
            />

            <div class="mt-4" v-if="breweries.data">
                <select v-model="per_page" @change="getResults(1, per_page)" class="border border-gray-300 rounded p-2">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
            </div>
        </div>

    </div>
</template>
