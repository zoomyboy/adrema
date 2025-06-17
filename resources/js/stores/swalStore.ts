import { defineStore } from 'pinia';
import { v4 as uuidv4 } from 'uuid';


interface Popup {
    id: string;
    title: string;
    body: string;
    confirmButton: string;
    cancelButton: string;
    resolve: (id: string) => void;
    reject: (id: string) => void;
}

export default defineStore('swal', {
    state: () => ({
        popups: [] as Popup[],
    }),
    actions: {
        confirm(title: string, body: string): Promise<void> {
            return new Promise((resolve, reject) =>  {
                new Promise<string>((resolve, reject) => {
                    this.popups.push({
                        title,
                        body,
                        confirmButton: 'Okay',
                        cancelButton: 'Abbrechen',
                        resolve,
                        reject,
                        id: uuidv4(),
                    });
                }).then((id) => {
                    this.remove(id);
                    resolve();
                }).catch((id) => {
                    this.remove(id);
                    reject();
                });
            });
        },

        remove(id: string) {
            this.popups = this.popups.filter(p => p.id !== id);
        }
    },
});
