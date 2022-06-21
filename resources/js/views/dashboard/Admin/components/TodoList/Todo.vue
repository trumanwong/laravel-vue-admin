<template>
  <li :class="{ completed: todo.done, editing: editing }" class="todo">
    <div class="view">
      <input :checked="todo.done" class="toggle" type="checkbox" @change="toggleTodo(todo)" />
      <label @dblclick="editing = true" v-text="todo.text" />
      <button class="destroy" @click="deleteTodo(todo)" />
    </div>
    <input
      v-show="editing"
      v-focus="editing"
      :value="todo.text"
      class="edit"
      @keyup.enter="doneEdit"
      @keyup.esc="cancelEdit"
      @blur="doneEdit"
    />
  </li>
</template>

<script>
export default {
  name: 'Todo',
  directives: {
    focus(el, { value }) {
      if (value) {
        nextTick(() => {
          el.focus()
        })
      }
    }
  },
  props: {
    todo: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  setup(props, ctx) {
    const resData = reactive({
      editing: false
    })
    const deleteTodo = (todo) => {
      ctx.emit('deleteTodo', todo)
    }
    const editTodo = ({ todo, value }) => {
      ctx.emit('editTodo', { todo, value })
    }
    const toggleTodo = (todo) => {
      ctx.emit('toggleTodo', todo)
    }
    const doneEdit = (e) => {
      const value = e.target.value.trim()
      const {todo} = props
      if (!value) {
        deleteTodo({
          todo
        })
      } else if (resData.editing) {
        editTodo({
          todo,
          value
        })
        resData.editing = false
      }
    }
    const cancelEdit = (e) => {
      e.target.value = props.todo.text
      resData.editing = false
    }

    return {
      ...toRefs(resData),
      deleteTodo,
      editTodo,
      toggleTodo,
      doneEdit,
      cancelEdit,
    }
  },
}
</script>
