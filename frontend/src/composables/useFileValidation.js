import * as pdfjsLib from "pdfjs-dist";

export function useFileValidation() {
  async function validatePDF(file, maxSizeMB = 5, maxPages = 5) {
    if (!file || file.type !== "application/pdf") {
      throw new Error("File must be a valid PDF.");
    }

    if (file.size > maxSizeMB * 1024 * 1024) {
      throw new Error(`File must not exceed ${maxSizeMB} MB.`);
    }

    const arrayBuffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;

    if (pdf.numPages > maxPages) {
      throw new Error(`File must not exceed ${maxPages} pages.`);
    }

    return true;
  }

  return { validatePDF };
}
