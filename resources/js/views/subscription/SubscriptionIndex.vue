<template>
    <div>
        <div class="custom-table">
            <header>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Name</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Interner Beitrag</div>
                <div class="px-6 text-gray-200 font-semibold py-3 border-gray-600 border-b">Nami-Beitrag</div>
                <div class="px-6 py-3 border-gray-600 border-b"></div>
            </header>

            <div v-for="sub, index in data" class="text-gray-200 transition-all duration-300 rounded flex items-center hover:bg-gray-800" :key="index">
                <div class="py-1 px-6" v-text="sub.name"></div>
                <div class="py-1 px-6" v-text="sub.amount_human"></div>
                <div class="py-1 px-6" v-text="sub.fee_name"></div>
                <div class="py-1 px-6 flex">
                    <Link :href="`/subscription/${sub.id}/edit`" class="inline-flex btn btn-warning btn-sm"><sprite src="pencil"></sprite></Link>
                </div>
            </div>

        </div>

    </div>
</template>

<script>
export default {
    props:{
        data: {}
    }
}
</script>
