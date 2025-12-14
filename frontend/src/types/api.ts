export interface ApiResponse<T = unknown> {
  message: string
  status: number
  data: T
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    from: number
    last_page: number
    per_page: number
    to: number
    total: number
  }
}

export interface ApiError {
  message: string
  status: number
  errors?: Record<string, string[]>
}