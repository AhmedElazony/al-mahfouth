<template>
  <div class="flex items-center gap-0.5 bg-gray-600 dark:bg-gray-600 rounded px-2 py-1" dir="ltr">
    <!-- Period (AM/PM) -->
    <select 
      v-model="period" 
      @change="updateTime"
      class="bg-transparent text-white text-center appearance-none cursor-pointer focus:outline-none text-xs w-6"
    >
      <option value="AM" class="bg-gray-800">ص</option>
      <option value="PM" class="bg-gray-800">م</option>
    </select>
    
    <!-- Hours (moved before minutes) -->
    <select 
      v-model="hours" 
      @change="updateTime"
      class="bg-transparent text-white text-center appearance-none cursor-pointer focus:outline-none w-6 text-xs"
    >
      <option value="" disabled class="bg-gray-800">--</option>
      <option v-for="h in hourOptions" :key="h" :value="h" class="bg-gray-800">
        {{ h }}
      </option>
    </select>
    
    <span class="text-gray-400 text-xs">:</span>
    
    <!-- Minutes (moved after hours) -->
    <select 
      v-model="minutes" 
      @change="updateTime"
      class="bg-transparent text-white text-center appearance-none cursor-pointer focus:outline-none w-6 text-xs"
    >
      <option value="" disabled class="bg-gray-800">--</option>
      <option v-for="m in minuteOptions" :key="m" :value="m" class="bg-gray-800">
        {{ m }}
      </option>
    </select>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'

interface Props {
  modelValue: string
  minuteStep?: number
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  minuteStep: 5
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const hours = ref<string>('')
const minutes = ref<string>('')
const period = ref<'AM' | 'PM'>('AM')

const hourOptions = Array.from({ length: 12 }, (_, i) => String(i + 1).padStart(2, '0'))
const minuteOptions = Array.from(
  { length: 60 / props.minuteStep }, 
  (_, i) => String(i * props.minuteStep).padStart(2, '0')
)

function parseTime(timeStr: string) {
  if (!timeStr) {
    hours.value = ''
    minutes.value = ''
    period.value = 'AM'
    return
  }

  const match = timeStr.match(/^(\d{1,2}):(\d{2})$/)
  if (!match) return

  let h = parseInt(match[1] || '0')
  const m = match[2]

  period.value = h >= 12 ? 'PM' : 'AM'
  if (h === 0) h = 12
  else if (h > 12) h -= 12
  hours.value = String(h).padStart(2, '0')

  const minNum = parseInt(m || '0')
  const roundedMin = Math.round(minNum / props.minuteStep) * props.minuteStep
  minutes.value = String(roundedMin % 60).padStart(2, '0')
}

function updateTime() {
  if (!hours.value || !minutes.value) return

  let h = parseInt(hours.value)
  if (period.value === 'PM' && h < 12) h += 12
  if (period.value === 'AM' && h === 12) h = 0

  emit('update:modelValue', `${String(h).padStart(2, '0')}:${minutes.value}`)
}

watch(() => props.modelValue, parseTime, { immediate: true })
onMounted(() => parseTime(props.modelValue))
</script>

<style scoped>
select { -webkit-appearance: none; -moz-appearance: none; }
</style>