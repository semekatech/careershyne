// src/api/campaign.js
import campaignAPI from '@/api/campaign';

export async function fetchCampaign(id) {
  const res = await campaignAPI.get();
  // find the matching one by id
  return res.data.find(item => item.id == id);
}
