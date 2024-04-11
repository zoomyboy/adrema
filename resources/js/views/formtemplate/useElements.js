export default function useElements() {
    function addOption(options) {
        return [...options, ''];
    }

    function setOption(options, index, $event) {
        return options.toSpliced(index, 1, $event);
    }

    function removeOption(options, index) {
        return options.toSpliced(index, 1);
    }

    return {addOption, setOption, removeOption};
}
