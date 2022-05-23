import {defineStore} from "pinia";

export const tagsViewStore = defineStore('tagsView', {
  state: () => {
    return {
      visitedViews: [],
      cachedViews: []
    }
  },
  actions: {
    addView(view) {
      this.addVisitedView(view)
      this.addCachedView(view)
    },
    addVisitedView(view) {
      this.$patch((state) => {
        if (state.visitedViews.some(v => v.path === view.path)) {
          return;
        }

        state.visitedViews.push(
          Object.assign(
            {},
            view,
            {
              title: view.meta.title || 'no-name',
            }
          )
        );
      })
    },
    addCachedView(view) {
      this.$patch((state) => {
        if (state.cachedViews.includes(view.name)) {
          return;
        }

        if (!view.meta.noCache) {
          state.cachedViews.push(view.name);
        }
      })
    },

    delView(view) {
      return new Promise(resolve => {
        this.delVisitedView(view)
        this.delCachedView(view)
        this.$patch((state) => {
          resolve({
            visitedViews: [...state.visitedViews],
            cachedViews: [...state.cachedViews],
          })
        })
      });
    },
    delVisitedView(view) {
      return new Promise(resolve => {
        this.$patch((state) => {
          for (const [i, v] of state.visitedViews.entries()) {
            if (v.path === view.path) {
              state.visitedViews.splice(i, 1)
              break
            }
          }
          resolve([...state.visitedViews])
        })
      });
    },
    delCachedView(view) {
      return new Promise(resolve => {
        this.$patch((state) => {
          for (const i of state.cachedViews) {
            if (i === view.name) {
              const index = state.cachedViews.indexOf(i);
              state.cachedViews.splice(index, 1);
              break;
            }
          }
          resolve([...state.cachedViews])
        })
      })
    },
    delOthersViews(view) {
      return new Promise(resolve => {
        this.delOthersVisitedViews(view)
        this.delOthersCachedViews(view)
        this.$patch((state) => {
          resolve({
            visitedViews: [...state.visitedViews],
            cachedViews: [...state.cachedViews],
          })
        })
      })
    },
    delOthersVisitedViews(view) {
      return new Promise(resolve => {
        this.$patch((state) => {
          state.visitedViews = state.visitedViews.filter(v => {
            return v.meta.affix || v.path === view.path
          })
          resolve([...state.visitedViews])
        })
      })
    },
    delOthersCachedViews( view) {
      return new Promise(resolve => {
        this.$patch((state) => {
          for (const i of state.cachedViews) {
            if (i === view.name) {
              const index = state.cachedViews.indexOf(i)
              state.cachedViews = state.cachedViews.slice(index, index + 1)
              break
            }
          }
          resolve([...state.cachedViews])
        })
      });
    },
    delAllViews(view) {
      return new Promise(resolve => {
        this.delAllVisitedViews()
        this.delAllCachedViews()
        this.$patch((state) => {
          resolve({
            visitedViews: [...state.visitedViews],
            cachedViews: [...state.cachedViews],
          })
        })
      })
    },
    delAllVisitedViews() {
      return new Promise(resolve => {
        this.$patch((state) => {
          // keep affix tags
          state.visitedViews = state.visitedViews.filter(tag => tag.meta.affix)
          resolve([...state.visitedViews])
        })
      });
    },
    delAllCachedViews() {
      return new Promise(resolve => {
        this.$patch((state) => {
          state.cachedViews = []
          resolve([...state.cachedViews])
        })
      });
    },
    updateVisitedView(view) {
      this.$patch((state) => {
        for (let v of state.visitedViews) {
          if (v.path === view.path) {
            v = Object.assign(v, view)
            break
          }
        }
      })
    },
  }
})
