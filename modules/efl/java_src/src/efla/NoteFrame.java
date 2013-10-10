/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * NoteFrame.java
 *
 * Created on 2009/10/22, 12:41:32 AM
 */
package efla;

import java.awt.Rectangle;
import javax.swing.text.BadLocationException;

/**
 *
 * @author kim
 */
public class NoteFrame extends javax.swing.JDialog {

    private EflaView eflaView;

    /** Creates new form NoteFrame */
    public NoteFrame(java.awt.Frame parent, boolean modal, EflaView eflaView) {
        super(parent, modal);
        initComponents();
        this.eflaView = eflaView;
        addButton.setText("Insert");
        closeButton.setText("Close");
    }

    private void addNote() {
        try {
            Rectangle rect = eflaView.getEssayField().modelToView(eflaView.getEssayField().getCaretPosition());
            rect.setSize(100, (int) rect.getHeight());
            eflaView.getEssayField().addNote(rect,noteField.getText());
            dispose();
        } catch (BadLocationException ex) {
            ex.printStackTrace();
        }
    }

    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        cPanel = new javax.swing.JPanel();
        addButton = new javax.swing.JButton();
        closeButton = new javax.swing.JButton();
        jScrollPane1 = new javax.swing.JScrollPane();
        noteField = new javax.swing.JTextArea();

        setDefaultCloseOperation(javax.swing.WindowConstants.DISPOSE_ON_CLOSE);
        setName("Form"); // NOI18N

        cPanel.setName("cPanel"); // NOI18N


        addButton.setName("addButton"); // NOI18N
        addButton.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                addButtonActionPerformed(evt);
            }
        });
        cPanel.add(addButton);


        closeButton.setName("closeButton"); // NOI18N
        closeButton.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                closeButtonActionPerformed(evt);
            }
        });
        cPanel.add(closeButton);

        getContentPane().add(cPanel, java.awt.BorderLayout.PAGE_END);

        jScrollPane1.setName("jScrollPane1"); // NOI18N

        noteField.setColumns(20);
        noteField.setLineWrap(true);
        noteField.setRows(5);
        noteField.setName("noteField"); // NOI18N
        jScrollPane1.setViewportView(noteField);

        getContentPane().add(jScrollPane1, java.awt.BorderLayout.CENTER);

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void closeButtonActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_closeButtonActionPerformed
        dispose();
    }//GEN-LAST:event_closeButtonActionPerformed

    private void addButtonActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_addButtonActionPerformed
        addNote();
    }//GEN-LAST:event_addButtonActionPerformed
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton addButton;
    private javax.swing.JPanel cPanel;
    private javax.swing.JButton closeButton;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTextArea noteField;
    // End of variables declaration//GEN-END:variables
}
