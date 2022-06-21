<template>
  <section class="todoapp">
    <!-- header -->
    <header class="header">
      <input class="new-todo" autocomplete="off" placeholder="Todo List" @keyup.enter="addTodo"/>
    </header>
    <!-- main section -->
    <section v-show="todos.length" class="main">
      <input
        id="toggle-all"
        :checked="allChecked"
        class="toggle-all"
        type="checkbox"
        @change="toggleAll({ done: !allChecked })"
      />
      <label for="toggle-all"/>
      <ul class="todo-list">
        <todo
          v-for="(todo, index) in filteredTodos"
          :key="index"
          :todo="todo"
          @toggleTodo="toggleTodo"
          @editTodo="editTodo"
          @deleteTodo="deleteTodo"
        />
      </ul>
    </section>
    <!-- footer -->
    <footer v-show="todos.length" class="footer">
      <span class="todo-count">
        <strong>{{ remaining }}</strong>
        {{ pluralize(remaining, 'item') }} left
      </span>
      <ul class="filters">
        <li v-for="(val, key) in filters" :key="key">
          <a :class="{ selected: visibility === key }" @click.prevent="visibility = key">{{ capitalize(key) }}</a>
        </li>
      </ul>
      <!-- <button class="clear-completed" v-show="todos.length > remaining" @click="clearCompleted">
        Clear completed
      </button> -->
    </footer>
  </section>
</template>

<script>
import Todo from './Todo.vue'

const STORAGE_KEY = 'todos'
const filters = {
  all: (todos) => todos,
  active: (todos) => todos.filter((todo) => !todo.done),
  completed: (todos) => todos.filter((todo) => todo.done)
}
const defalutList = [
  {text: 'star this repository', done: false},
  {text: 'fork this repository', done: false},
  {text: 'follow author', done: false},
  {text: 'vue3-admin-plus', done: true},
  {text: 'vue', done: true},
  {text: 'element-ui', done: true},
  {text: 'axios', done: true},
  {text: 'webpack', done: true}
]
export default {
  components: {Todo},
  setup() {
    const resData = reactive({
      visibility: 'all',
      filters,
      // todos: JSON.parse(window.localStorage.getItem(STORAGE_KEY)) || defalutList
      todos: defalutList,
      allChecked: computed(() => {
        return resData.todos.every((todo) => todo.done)
      }),
      filteredTodos: computed(() => {
        return filters[resData.visibility](resData.todos)
      }),
      remaining: computed(() => {
        return resData.todos.filter((todo) => !todo.done).length
      })
    })

    const pluralize = (n, w) => {
      return n === 1 ? w : w + 's'
    }
    const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1)
    const setLocalStorage = () => {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(resData.todos))
    }
    const addTodo = (e) => {
      const text = e.target.value
      if (text.trim()) {
        resData.todos.push({
          text,
          done: false
        })
        setLocalStorage()
      }
      e.target.value = ''
    }
    const toggleTodo = (val) => {
      val.done = !val.done
      setLocalStorage()
    }
    const deleteTodo = (todo) => {
      resData.todos.splice(resData.todos.indexOf(todo), 1)
      setLocalStorage()
    }
    const editTodo = ({todo, value}) => {
      todo.text = value
      setLocalStorage()
    }
    const clearCompleted = () => {
      resData.todos = resData.todos.filter((todo) => !todo.done)
      setLocalStorage()
    }
    const toggleAll = ({done}) => {
      resData.todos.forEach((todo) => {
        todo.done = done
        setLocalStorage()
      })
    }

    return {
      ...toRefs(resData),
      pluralize,
      capitalize,
      setLocalStorage,
      addTodo,
      toggleTodo,
      deleteTodo,
      editTodo,
      clearCompleted,
      toggleAll
    }
  },
}
</script>

<style lang="scss">
@import './index.scss';
</style>
