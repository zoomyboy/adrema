<template>
    <div class="p-6">
        <div class="text-lg">Deine App wurde noch nicht initialisiert. Nami-Daten jetzt übernehmen?</div>
        <a class="btn btn-primary mt-6 inline-block" href="#" @click.prevent="$inertia.post('/initialize', { preserveState: true, replace: false })">Jetzt initialisieren</a>
    </div>
</template>

<script>
import App from '../../layouts/App';

export default {
    
    layout: App

};
</script>
