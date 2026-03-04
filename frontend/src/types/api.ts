export interface ApiResponse<T> {
  message: string
  data: T
  status: number
}

export interface PaginatedResponse<T> {
  message: string
  data: T[]
  pagination: PaginationMeta
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
