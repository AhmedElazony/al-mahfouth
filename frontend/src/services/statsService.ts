import api from './api'

export interface SystemStats {
	groups_count: number
	students_count: number
	teachers_count: number
	reports_count: number
	attended_count: number
	absent_count: number
	excused_count: number
}

export interface GroupStats {
	group_id: number
	students_count: number
	committed_count: number
	uncommitted_count: number
	absent_count: number
}

class StatsService {
	async getSystemStats(): Promise<{ data: SystemStats }> {
		const response = await api.get('/admin/stats')
		return response.data
	}

	async getGroupStats(groupId: number): Promise<{ data: GroupStats }> {
		const response = await api.get(`/admin/groups/${groupId}/stats`)
		return response.data
	}
}

export default new StatsService()