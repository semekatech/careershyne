import { onMounted } from "vue";
import { logVisitor } from "@/services/visitorService";

export function useVisitorLogger(pageName) {
  onMounted(async () => {
    try {
      const data = await logVisitor(pageName);
      console.log("Visitor logged:", data);
    } catch (error) {
      console.error("Failed to log visitor");
    }
  });
}
